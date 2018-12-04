<?php
include 'Inputs.php';

class DayOne {
    private $input;

    public function __construct()
    {
        $this->input = $_SESSION['dayOneInput'];
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

class DayTwo {
    private $input;

    public function __construct()
    {
        $this->input = $_SESSION['dayTwoInput'];
    }

    public function firstStar()
    {
        $threeOfAKindCount = 0;
        $twoOfAKindCount = 0;

        foreach ($this->input as $string) {
            $footprint = $this->getRepetitionFootprint($string);
            if ($footprint['hasThreeOfAKind']) {
                $threeOfAKindCount++;
            }
            if ($footprint['hasTwoOfAKind']) {
                $twoOfAKindCount++;
            }
        }

        return $threeOfAKindCount * $twoOfAKindCount;
    }

    protected function getRepetitionFootprint($string)
    {
        $characterCounts = array_count_values(str_split($string));

        $hasTwoOfAKind = count(array_filter($characterCounts, function ($count) {
           return $count === 2;
        })) > 0;

        $hasThreeOfAKind = count(array_filter($characterCounts, function ($count) {
                return $count === 3;
            })) > 0;

        return [
            'hasTwoOfAKind' => $hasTwoOfAKind,
            'hasThreeOfAKind' => $hasThreeOfAKind
        ];
    }

    public function secondStar()
    {
        foreach ($this->input as $baseString) {
            foreach ($this->input as $otherString) {
                // Find two strings that differ by only one character
                if (similar_text($baseString, $otherString) - strlen($baseString) === -1) {
                    // Return the common letters
                    return $this->getCommonLetters($baseString, $otherString);
                }
            }
        }
    }

    protected function getCommonLetters($stringOne, $stringTwo)
    {
        return implode(array_intersect(str_split($stringOne), str_split($stringTwo)));
    }
}


$master = new DayTwo();
var_dump($master->secondStar());
