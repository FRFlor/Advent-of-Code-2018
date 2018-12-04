<?php
include 'Inputs.php';

class DayOne {
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

class DayTwo {
    private $input;

    public function __construct()
    {
        $this->input = getDayTwoInputs();
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

class DayThree {
    private $input;
    private $canvas;
    const MAX_X = 1000;
    const MAX_Y = 1000;

    public function __construct()
    {
        $this->input = getDayThreeInputs();

        for($i = 0; $i < self::MAX_X; $i++){
            for($j = 0; $j < self::MAX_Y; $j++){
                $this->canvas[$i][$j] = 0;
            }
        }
    }

    public function firstStar()
    {
        foreach ($this->input as $instruction) {
            $this->draw($instruction);
        }

        return $this->countRepetitions();
    }

    protected function draw($instruction)
    {
        preg_match("/#\d+ @ (\d+),(\d+): (\d+)x(\d+)/",$instruction, $matches);
        $startColumn = (int) $matches[1];
        $startRow = (int) $matches[2];
        $length = (int) $matches[3];
        $height = (int) $matches[4];
        $endColumn = $startColumn + $length;
        $endRow = $startRow + $height;

        for($column = $startColumn; $column < $endColumn; $column++){
            for($row = $startRow; $row < $endRow; $row++){
                $this->canvas[$column][$row]++;
            }
        }
    }

    protected function countRepetitions()
    {
        $repetitions = 0;
        for($column = 0; $column < self::MAX_X; $column++){
            for($row = 0; $row < self::MAX_Y; $row++){
                if ($this->canvas[$column][$row] > 1) {
                    $repetitions++;
                }
            }
        }

        return $repetitions;
    }
}

$master = new DayThree();
var_dump($master->firstStar());
