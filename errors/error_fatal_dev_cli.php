<?php

echo "\n";
echo 'ERROR:   FATAL' . "\n";
echo 'SUBJECT: ' . strip_tags($subject) . "\n";
echo 'MESSAGE: ' . strip_tags($message) . "\n";
echo "\n";
echo 'BACKTRACE:' . "\n";
$backtrace = array();
foreach ($details->backtrace as $bt) {

    $file  = !empty($bt['file']) ? $bt['file'] : '<unknown>';
    $line  = !empty($bt['line']) ? $bt['line'] : '<unknown>';
    $class = !empty($bt['class']) ? $bt['class'] . '->' : '';
    $func  = !empty($bt['function']) ? $bt['function'] : '<unknown>';

    echo 'File "' . $file . '" line ' . $line . ' in ' . $class . $func . '' . "\n";
}
echo "\n";
