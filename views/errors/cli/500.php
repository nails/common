<?php

use Nails\Environment;

echo "\n";
if (Environment::not('PRODUCTION')) {

    echo 'ERROR:   FATAL' . "\n";
    echo 'SUBJECT: ' . strip_tags($sSubject) . "\n";
    echo 'MESSAGE: ' . strip_tags($sMessage) . "\n";
    echo "\n";
    echo 'BACKTRACE:' . "\n";

    foreach ($oDetails->backtrace as $bt) {
        $file  = !empty($bt['file']) ? $bt['file'] : '<unknown>';
        $line  = !empty($bt['line']) ? $bt['line'] : '<unknown>';
        $class = !empty($bt['class']) ? $bt['class'] . '->' : '';
        $func  = !empty($bt['function']) ? $bt['function'] : '<unknown>';

        echo 'File "' . $file . '" line ' . $line . ' in ' . $class . $func . '' . "\n";
    }

} else {
    echo 'Sorry, an error occurred which we couldn\'t recover from. The technical team have been informed, ';
    echo 'we apologise for the inconvenience.';
}
echo "\n";
