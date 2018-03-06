<?php

echo "\n";
echo 'ERROR:   FATAL' . "\n";
echo 'SUBJECT: ' . strip_tags($sSubject) . "\n";
echo 'MESSAGE: ' . strip_tags($sMessage) . "\n";
echo "\n";
echo 'BACKTRACE:' . "\n";
foreach ($oDetails->backtrace as $aItem) {

    $sFile  = !empty($aItem['file']) ? $aItem['file'] : '<unknown>';
    $sLine  = !empty($aItem['line']) ? $aItem['line'] : '<unknown>';
    $sClass = !empty($aItem['class']) ? $aItem['class'] . '->' : '';
    $sFunc  = !empty($aItem['function']) ? $aItem['function'] : '<unknown>';

    echo 'File "' . $sFile . '" line ' . $sLine . ' in ' . $sClass . $sFunc . '' . "\n";
}
echo "\n";
