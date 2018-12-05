<?php
include_once "Timer.php";

// Auto-loading the answer classes
spl_autoload_register(function ($class) {
    include_once "inputs.php";
    include "Answers/$class.php";
});

$fullTimer = new Timer();
$fullTimer->start();

// ------------------------------------------------------------
// -- Instantiate whatever answer you want to execute here --
// ------------------------------------------------------------
$answer = new DayFive();

$functionTimer = new Timer();
fwrite(STDOUT, "First Star: ");
$functionTimer->start();
fwrite(STDOUT, "Done! Answer = {$answer->firstStar()} ({$functionTimer->elapsed()} s)\n");

fwrite(STDOUT, "Second Star: ");
$functionTimer->restart();
fwrite(STDOUT, "Done!  Answer = {$answer->secondStar()}) ({$functionTimer->elapsed()} s)\n");

fwrite(STDOUT, "Total Execution Time: {$fullTimer->elapsed()}s\n");
