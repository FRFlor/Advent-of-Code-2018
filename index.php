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
foreach (['DaySix'] as $className) {
    fwrite(STDOUT, "====== $className ======\n\n");
    $answer = new $className();

    $functionTimer = new Timer();
    fwrite(STDOUT, "First Star: ");
    $functionTimer->start();
    fwrite(STDOUT, "Done! Answer = {$answer->firstStar()} ({$functionTimer->elapsed()} s)\n");

    fwrite(STDOUT, "Second Star: ");
    $functionTimer->restart();
    fwrite(STDOUT, "Done!  Answer = {$answer->secondStar()} ({$functionTimer->elapsed()} s)\n\n");
}

fwrite(STDOUT, "====== DONE ======\n");
fwrite(STDOUT, "Total Execution Time: {$fullTimer->elapsed()}s\n");
