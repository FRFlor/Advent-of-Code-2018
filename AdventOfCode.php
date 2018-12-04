<?php
include 'Inputs.php';

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

class DayTwo
{
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

    protected function getCommonLetters($stringOne, $stringTwo)
    {
        return implode(array_intersect(str_split($stringOne), str_split($stringTwo)));
    }
}

class DayThree
{
    private $input;
    private $canvas;
    const MAX_X = 1000;
    const MAX_Y = 1000;

    public function __construct()
    {
        $this->input = getDayThreeInputs();

        for ($i = 0; $i < self::MAX_X; $i++) {
            for ($j = 0; $j < self::MAX_Y; $j++) {
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

    public function secondStar()
    {
        foreach ($this->input as $instruction) {
            $this->draw($instruction);
        }

        return $this->getFirstIdWithoutIntersections();
    }

    protected function parseInstruction($instruction)
    {
        preg_match("/#(\d+) @ (\d+),(\d+): (\d+)x(\d+)/", $instruction, $matches);

        $id = (int)$matches[1];
        $startColumn = (int)$matches[2];
        $startRow = (int)$matches[3];
        $length = (int)$matches[4];
        $height = (int)$matches[5];
        $endColumn = $startColumn + $length;
        $endRow = $startRow + $height;

        return [$id, $startColumn, $endColumn, $startRow, $endRow];
    }

    protected function draw($instruction)
    {
        [$id, $startColumn, $endColumn, $startRow, $endRow] = $this->parseInstruction($instruction);

        for ($column = $startColumn; $column < $endColumn; $column++) {
            for ($row = $startRow; $row < $endRow; $row++) {
                $this->canvas[$column][$row]++;
            }
        }
    }

    protected function countRepetitions()
    {
        $repetitions = 0;
        for ($column = 0; $column < self::MAX_X; $column++) {
            for ($row = 0; $row < self::MAX_Y; $row++) {
                if ($this->canvas[$column][$row] > 1) {
                    $repetitions++;
                }
            }
        }

        return $repetitions;
    }

    protected function getFirstIdWithoutIntersections()
    {
        foreach ($this->input as $instruction) {
            [$id, $startColumn, $endColumn, $startRow, $endRow] = $this->parseInstruction($instruction);

            $hasNoIntersections = true;
            for ($column = $startColumn; $column < $endColumn; $column++) {
                for ($row = $startRow; $row < $endRow; $row++) {
                    if ($this->canvas[$column][$row] > 1) {
                        $hasNoIntersections = false;
                    }
                }
            }

            if ($hasNoIntersections) {
                return $id;
            }
        }
    }
}

class DayFour {
    private $input;
    private $guardsSchedule = [];

    public function __construct()
    {
        $this->input = getDayFourInputs();
        $this->sortGuardsShifts();
    }

    public function firstStar()
    {
        $guardsSleepAmount = $this->getMinutesSleptByEachGuard();

        // Find the Guard that has slept the most
        $greatestSleep = 0;
        $greatestSleeper = null;
        foreach ($guardsSleepAmount as $guardId => $sleepTime) {
            if ($sleepTime > $greatestSleep) {
                $greatestSleeper = $guardId;
                $greatestSleep = $sleepTime;
            }
        }

        // Find the minute most slept by the guard
        $minutesArray = $this->getSleepDistributionForGuard($greatestSleeper);
        $highestCount = 0;
        $mostSleptMinute = null;
        foreach ($minutesArray as $minute => $count) {
            if ($count > $highestCount) {
                $mostSleptMinute = $minute;
                $highestCount = $count;
            }
        }

        return $mostSleptMinute * $greatestSleeper;
    }

    public function secondStar()
    {
        // Find the minute most slept by the guard
        $guardSleepDist = $this->getSleepDistributionForAllGuards();
        $highestCount = 0;
        $mostSleptMinute = null;
        $sleepingGuard = null;
        foreach ($guardSleepDist as $guardId => $minutes) {
            foreach ($minutes as $minute => $count) {
                if ($count > $highestCount) {
                    $mostSleptMinute = $minute;
                    $highestCount = $count;
                    $sleepingGuard = $guardId;
                }
            }
        }

       return $sleepingGuard * $mostSleptMinute;
    }

    protected function getMinutesDifference($date1, $date2)
    {
        $interval = $date1->diff($date2);

        return $interval->h * 60 + $interval->i;
    }

    protected function sortGuardsShifts()
    {
        foreach ($this->input as $line) {
            array_push($this->guardsSchedule, $this->parseInputLine($line));
        }

        usort($this->guardsSchedule, function ($a, $b) {
            return $b[0] < $a[0];
        });
    }

    protected function getSleepDistributionForAllGuards()
    {
        $minutes = [];
        $startToSleep = null;
        $wakesUp = null;
        $guardId = null;
        foreach ($this->guardsSchedule as $entry) {
            if (strpos($entry[1], 'Guard') !== false) {
                preg_match("/Guard #(\d+) .+/", $entry[1], $matches);
                $guardId = (int) $matches[1];
                $minutes[$guardId] = $minutes[$guardId] ?? [];
            }
            elseif (strpos($entry[1], 'falls') !== false) {
                $startToSleep = $entry[0];
            }
            elseif (strpos($entry[1], 'wakes') !== false) {
                $wakesUp = $entry[0];

                $startHour = (int) $startToSleep->format("H");
                $startMinute = (int) $startToSleep->format("i");
                $endHour = (int) $wakesUp->format("H");
                $endMinute = (int) $wakesUp->format("i");

                $hour = $startHour;
                $minute =  $startMinute;
                while ($hour !== $endHour || $minute !== $endMinute) {
                    $minutes[$guardId][$minute] = ($minutes[$guardId][$minute] ?? 0) + 1;

                    // Increment Time
                    $minute++;
                    if ($minute > 59) {
                        $hour++;
                        $minute = 0;
                        if ($hour > 23) {
                            $hour = 0;
                            $minute = 0;
                        }
                    }
                }
            }
        }

        return $minutes;
    }

    protected function getSleepDistributionForGuard($guardId)
    {
        $minutes = [];
        $startToSleep = null;
        $wakesUp = null;

        $isRightGuard = false;
        foreach ($this->guardsSchedule as $entry) {
            if (strpos($entry[1], 'Guard') !== false) {
                preg_match("/Guard #(\d+) .+/", $entry[1], $matches);
                $thisGuardId = (int) $matches[1];
                $isRightGuard = ($thisGuardId === $guardId);
            }
            elseif (strpos($entry[1], 'falls') !== false && $isRightGuard) {
                $startToSleep = $entry[0];
            }
            elseif (strpos($entry[1], 'wakes') !== false && $isRightGuard) {
                $wakesUp = $entry[0];

                $startHour = (int) $startToSleep->format("H");
                $startMinute = (int) $startToSleep->format("i");
                $endHour = (int) $wakesUp->format("H");
                $endMinute = (int) $wakesUp->format("i");

                $hour = $startHour;
                $minute =  $startMinute;
                while ($hour !== $endHour || $minute !== $endMinute) {
                    $minutes[$minute] = ($minutes[$minute] ?? 0) + 1;

                    // Increment Time
                    $minute++;
                    if ($minute > 59) {
                        $hour++;
                        $minute = 0;
                        if ($hour > 23) {
                            $hour = 0;
                            $minute = 0;
                        }
                    }
                }
            }
        }

        return $minutes;

    }

    protected function getMinutesSleptByEachGuard()
    {
        $guardId = null;
        $startToSleep = null;
        $wakesUp = null;

        $guardsSleepTrack = [];
        foreach ($this->guardsSchedule as $entry) {
            if (strpos($entry[1], 'Guard') !== false) {
                preg_match("/Guard #(\d+) .+/", $entry[1], $matches);
                $guardId = $matches[1];
            }
            elseif (strpos($entry[1], 'falls') !== false) {
                $startToSleep = $entry[0];
            }
            elseif (strpos($entry[1], 'wakes') !== false) {
                $wakesUp = $entry[0];
                $guardsSleepTrack[$guardId] = ($guardsSleepTrack[$guardId] ?? 0) + $this->getMinutesDifference($startToSleep, $wakesUp);
            }
        }

        return $guardsSleepTrack;
    }

    protected function parseInputLine($line)
    {
        preg_match("/\[(.+)\] (.+)/", $line, $matches);
        $time = new DateTime($matches[1]);
        $event = $matches[2];

        return [$time, $event];
    }
}

$master = new DayFour();
var_dump($master->secondStar());
