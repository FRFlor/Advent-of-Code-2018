<?php

class DayOne
{
    private $input;

    public function __construct()
    {
        $this->input = getDayOneInputs();
    }

    public function firstStar()
    {
        return array_sum($this->input);
    }

    public function secondStar()
    {
        $index = 0; // Pointer for the input array
        $allFrequencies = []; // Container for all frequencies that have occurred so far
        $currentFrequency = 0; // The newest frequency entry

        $allFrequencies[0] = 1; // Register the starting frequency
        while (true) {
            $delta = $this->input[$index];
            $currentFrequency += $delta;

            // If this frequency already existed before...
            if (($allFrequencies[$currentFrequency] ?? null) !== null) {
                return $currentFrequency;  // Game Over!
            }

            // Otherwise... Register this frequency
            $allFrequencies[$currentFrequency] = 1;

            // Keep repeating the loop as necessary
            $index = ($index === count($this->input) - 1) ? 0 : $index + 1;
        }
    }
}
