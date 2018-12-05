<?php

// Auto-loading the answer classes
spl_autoload_register(function ($class) {
    include_once "inputs.php";
    include "Answers/$class.php";
});

// ------------------------------------------------------------
// -- Instantiate whatever answer you want to execute here --
// ------------------------------------------------------------
$timeStart = microtime(true);
$answer = new DayFive();

fwrite(STDOUT, "First Star: {$answer->firstStar() }\n");
fwrite(STDOUT, "Second Star: {$answer->secondStar() }\n");

$elapsedSeconds = microtime(true) - $timeStart;
fwrite(STDOUT, "Total Execution Time: $elapsedSeconds\n");
