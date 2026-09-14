#!/usr/bin/env php
<?php

/**
 * Verifies that a component's migrations remain reachable for apps arriving from
 * another long-lived branch.
 *
 * db:migrate records one version integer per component and runs only the migrations
 * numbered above it. Where a component is maintained on two long-lived branches, that
 * integer is written by one branch and read by the other, so a migration can be skipped
 * entirely. Two things keep a component safe:
 *
 *  1. A change present on both branches must carry the same number on both, so an app
 *     moving between them neither skips it nor applies it twice. In practice that means
 *     taking the next number from a single line shared by both branches - whichever
 *     branch is behind simply ends up with a gap, which costs nothing.
 *  2. A change present on only one branch can never be made safe by numbering, because
 *     the other branch may later number a shared change above it. It must implement
 *     Nails\Common\Interfaces\Database\Migration\Repeatable so that it is evaluated on
 *     every run regardless of the recorded version.
 *
 * This script enforces both, and reports the next number that is free on every branch.
 *
 * Numbering that diverged before the shared line was adopted is waived explicitly, by
 * number and by the shape of the divergence - "differs" where both branches use the
 * number for different changes, "absent" where only this branch has it. Recording the
 * shape keeps the waiver narrow: if the counterpart later puts its own change at a
 * number waived as "absent", the shape no longer matches and the check speaks up. The
 * list should only ever shrink. Configure via composer.json:
 *
 *     "extra": { "nails": { "migrations": {
 *         "grandfathered": { "17": "differs", "21": "absent" },
 *         "branches": ["develop", "feature/pre-new-admin"]
 *     } } }
 *
 * Usage: php check-migration-numbering.php [--repo=<path>] [--branch=<name>]
 *                                         [--exclude=<name>] [--strict]
 *
 * --exclude names the line being merged into, which is not a counterpart to compare
 * against; CI should pass the pull request's base branch. --strict waives nothing.
 */

const DEFAULT_BRANCHES = ['develop', 'feature/pre-new-admin'];

/**
 * Migrations sit under Database/Migration, but how deep varies: modules use
 * src/Database/Migration, common uses src/Common/Database/Migration, and drivers
 * nest under their own namespace, as in src/Stripe/Database/Migration.
 */
const MIGRATION_PATTERN = '#(^|/)Database/Migration/Migration(\d+)\.php$#';

// --------------------------------------------------------------------------

$sRepo     = getcwd();
$aBranches = [];
$aExclude  = [];
$aOld      = null;
$bEmit     = false;
$sHead     = null;

foreach (array_slice($argv, 1) as $sArg) {
    if (preg_match('/^--repo=(.+)$/', $sArg, $aM)) {
        $sRepo = rtrim($aM[1], '/');
    } elseif (preg_match('/^--branch=(.+)$/', $sArg, $aM)) {
        $aBranches[] = $aM[1];
    } elseif (preg_match('/^--exclude=(.*)$/', $sArg, $aM)) {
        $aExclude[] = $aM[1];
    } elseif (preg_match('/^--head=(.+)$/', $sArg, $aM)) {
        $sHead = $aM[1];
    } elseif ($sArg === '--strict') {
        $aOld = [];
    } elseif ($sArg === '--emit-waiver') {
        $aOld     = [];
        $bEmit    = true;
    } else {
        fwrite(STDERR, 'Unrecognised argument: ' . $sArg . PHP_EOL);
        exit(2);
    }
}

// --------------------------------------------------------------------------

/**
 * Runs a git command in the repo, returning its output lines
 */
function git(string $sRepo, array $aArgs, ?int &$iExit = null): array
{
    $sCommand = 'git -C ' . escapeshellarg($sRepo);
    foreach ($aArgs as $sArg) {
        $sCommand .= ' ' . escapeshellarg($sArg);
    }

    exec($sCommand . ' 2>/dev/null', $aOut, $iExit);

    return $aOut;
}

/**
 * Returns the migrations on a branch (or in the working tree) as number => contents
 */
function migrations(string $sRepo, ?string $sRef): array
{
    $aOut = [];

    if ($sRef === null) {

        $oFiles = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sRepo . '/src', FilesystemIterator::SKIP_DOTS)
        );

        foreach ($oFiles as $oFile) {
            $sPath = str_replace('\\', '/', $oFile->getPathname());
            if (preg_match(MIGRATION_PATTERN, $sPath, $aM)) {
                $aOut[(int) $aM[2]] = file_get_contents($oFile->getPathname());
            }
        }

    } else {

        foreach (git($sRepo, ['ls-tree', '-r', '--name-only', $sRef, 'src/']) as $sPath) {
            if (preg_match(MIGRATION_PATTERN, $sPath, $aM)) {
                $aOut[(int) $aM[2]] = implode("\n", git($sRepo, ['show', $sRef . ':' . $sPath]));
            }
        }
    }

    ksort($aOut);

    return $aOut;
}

/**
 * Resolves a branch name to a ref which exists locally
 *
 * CI checkouts commonly have no local branch for the counterpart line, only the remote
 * tracking ref, so fall back to that.
 *
 * @return string|null The usable ref, or null if the branch is not present at all
 */
function resolveRef(string $sRepo, string $sBranch): ?string
{
    foreach ([$sBranch, 'origin/' . $sBranch] as $sRef) {
        $iExit = null;
        git($sRepo, ['rev-parse', '--verify', '--quiet', $sRef . '^{commit}'], $iExit);
        if ($iExit === 0) {
            return $sRef;
        }
    }

    return null;
}

/**
 * Whether a migration's source declares itself repeatable
 */
function isRepeatable(string $sBody): bool
{
    return (bool) preg_match('/Migration\\\\Repeatable|implements\s+Repeatable\b/', $sBody);
}

/**
 * Fingerprints what a migration actually does
 *
 * Two branches hold "the same change" if the code is the same; comments and layout
 * drift between long-lived branches (docblock @package tags especially) and must not
 * register as a difference.
 */
function fingerprint(string $sBody): string
{
    $aKeep = [];

    foreach (token_get_all($sBody) as $mToken) {

        if (is_array($mToken)) {

            if (in_array($mToken[0], [T_COMMENT, T_DOC_COMMENT, T_WHITESPACE], true)) {
                continue;
            }

            $aKeep[] = $mToken[1];

        } else {
            $aKeep[] = $mToken;
        }
    }

    return md5(implode("\x00", $aKeep));
}

// --------------------------------------------------------------------------

/**
 * Normally the work in hand is whatever is checked out; --head reads it from a ref
 * instead, for auditing a branch without checking it out
 */
$sLocalRef = null;

if ($sHead !== null) {

    $sLocalRef = resolveRef($sRepo, $sHead);

    if ($sLocalRef === null) {
        fwrite(STDERR, basename($sRepo) . ': ' . $sHead . ' not present' . PHP_EOL);
        exit(2);
    }
}

//  Configuration travels with the work being checked, so read it from the same place
if ($sLocalRef === null) {
    $sComposer = is_file($sRepo . '/composer.json')
        ? file_get_contents($sRepo . '/composer.json')
        : '';
} else {
    $sComposer = implode("\n", git($sRepo, ['show', $sLocalRef . ':composer.json']));
}

$aComposer = json_decode($sComposer, true) ?: [];
$aConfig   = $aComposer['extra']['nails']['migrations'] ?? [];

$aOld      = $aOld ?? $aConfig['grandfathered'] ?? [];
$aBranches = $aBranches ?: ($aConfig['branches'] ?? DEFAULT_BRANCHES);

$sName = $aComposer['name'] ?? basename($sRepo);

// --------------------------------------------------------------------------

$aLocal = migrations($sRepo, $sLocalRef);

if (empty($aLocal)) {
    echo $sName . ': no migrations, nothing to check' . PHP_EOL;
    exit(0);
}

/**
 * The line this work is destined for is not a counterpart to compare against: it will
 * not have the new migration yet, and once merged the two are the same line.
 */
$aExclude[] = $sHead ?? (git($sRepo, ['rev-parse', '--abbrev-ref', 'HEAD'])[0] ?? '');
$aExclude   = array_filter($aExclude);

$aErrors   = [];
$aWarnings = [];
$aChecked  = [];
$aFound    = [];
$iHighest  = max(array_keys($aLocal));

foreach ($aBranches as $sBranch) {

    if (in_array($sBranch, $aExclude, true)) {
        continue;
    }

    $sRef = resolveRef($sRepo, $sBranch);
    if ($sRef === null) {
        echo $sName . ': ' . $sBranch . ' not present, skipping' . PHP_EOL;
        continue;
    }

    $aOther     = migrations($sRepo, $sRef);
    $aChecked[] = $sRef;
    $iHighest   = max($iHighest, ...(array_keys($aOther) ?: [0]));

    $iOtherMax = $aOther ? max(array_keys($aOther)) : -1;

    foreach ($aLocal as $iNumber => $sBody) {

        if (isRepeatable($sBody)) {
            continue;
        }

        /**
         * How this number diverges from the counterpart, if at all. Recording the shape
         * rather than just the number means a known divergence can be waived without
         * also waiving a future, different mistake at the same number.
         */
        if (!array_key_exists($iNumber, $aOther)) {
            $sShape = 'absent';
        } elseif (fingerprint($sBody) !== fingerprint($aOther[$iNumber])) {
            $sShape = 'differs';
        } else {
            continue;
        }

        $aFound[(string) $iNumber] = $sShape;

        if (($aOld[(string) $iNumber] ?? null) === $sShape) {
            continue;
        }

        if ($sShape === 'differs') {
            $aErrors[] = sprintf(
                "Migration%d holds a different change on %s.\n"
                . "      One number cannot mean two things; an app moving between branches\n"
                . "      will run one of them and never the other. Renumber so that each\n"
                . "      change has exactly one number.",
                $iNumber,
                $sBranch
            );

        } elseif ($iNumber <= $iOtherMax) {

            //  Unreachable already, because that branch records a version above this
            $aErrors[] = sprintf(
                "Migration%d is unreachable for apps arriving from %s.\n"
                . "      That branch already reaches %d, so such an app records a version at or\n"
                . "      above %d and this migration never runs. Give the change the same number\n"
                . "      on both branches, or make it Repeatable.",
                $iNumber,
                $sBranch,
                $iOtherMax,
                $iNumber
            );

        } else {

            //  Safe today, but only until the counterpart numbers a change above it
            $aWarnings[] = sprintf(
                "Migration%d is not on %s.\n"
                . "      It runs today, but will be skipped by apps arriving from that branch as\n"
                . "      soon as it numbers a change above %d. Port it across with the same\n"
                . "      number, or make it Repeatable.",
                $iNumber,
                $sBranch,
                $iNumber
            );
        }
    }
}

// --------------------------------------------------------------------------

/**
 * Adopting the current state as the waiver, for bringing an already-diverged branch
 * under the check without first reconciling its history
 */
if ($bEmit) {
    ksort($aFound, SORT_NUMERIC);
    echo json_encode(['grandfathered' => $aFound, 'next' => $iHighest + 1]) . PHP_EOL;
    exit(0);
}

echo $sName . ': checked ' . count($aLocal) . ' migrations';
echo $aChecked ? ' against ' . implode(', ', $aChecked) : ' (no counterpart branch present)';
echo $aOld ? ', ignoring ' . count($aOld) . ' grandfathered' : '';
echo PHP_EOL;

if ($aErrors || $aWarnings) {
    echo PHP_EOL;
    foreach ($aErrors as $sError) {
        echo '  ERROR ' . $sError . PHP_EOL . PHP_EOL;
    }
    foreach ($aWarnings as $sWarning) {
        echo '  WARN  ' . $sWarning . PHP_EOL . PHP_EOL;
    }
} else {
    echo 'OK. ';
}

echo 'The next number free on every branch is ' . ($iHighest + 1) . '.' . PHP_EOL;

exit($aErrors ? 1 : 0);
