<?php

// Auto-loading the answer classes
spl_autoload_register(function ($class) {
    include_once "inputs.php";
    include "Answers/$class.php";
});

// ------------------------------------------------------------
// -- Instantiate whatever answer you want to execute here --
// ------------------------------------------------------------
$answer = new DayFour();

fwrite(STDOUT, "First Star: {$answer->firstStar()}\n");
fwrite(STDOUT, "Second Star: {$answer->secondStar()}\n");
