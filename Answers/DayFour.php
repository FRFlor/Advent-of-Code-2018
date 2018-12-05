<?php

class DayFour
{
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

        // Find the minute most slept by the greatest sleeper
        $minutesArray = $this->getSleepDistributionForGuards($greatestSleeper);
        $highestCount = 0;
        $mostSleptMinute = null;
        foreach ($minutesArray[$greatestSleeper] as $minute => $count) {
            if ($count > $highestCount) {
                $mostSleptMinute = $minute;
                $highestCount = $count;
            }
        }

        return $mostSleptMinute * $greatestSleeper;
    }

    public function secondStar()
    {
        // Get the sleep distribution for all the guards
        $guardSleepDist = $this->getSleepDistributionForGuards();

        // Find the most slept minutes of all and the guard behind it
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

    protected function getSleepDistributionForGuards($targetGuardId = null)
    {
        $minutes = [];
        $startToSleep = null;
        $wakesUp = null;
        $thisGuardId = null;

        $isRightGuard = false;
        foreach ($this->guardsSchedule as $entry) {
            if (strpos($entry[1], 'Guard') !== false) {
                preg_match("/Guard #(\d+) .+/", $entry[1], $matches);
                $thisGuardId = (int)$matches[1];
                $isRightGuard = ($targetGuardId === null || $thisGuardId === $targetGuardId);
                if ($isRightGuard) {
                    $minutes[$thisGuardId] = $minutes[$thisGuardId] ?? [];
                }
            } elseif (strpos($entry[1], 'falls') !== false && $isRightGuard) {
                $startToSleep = $entry[0];
            } elseif (strpos($entry[1], 'wakes') !== false && $isRightGuard) {
                $wakesUp = $entry[0];

                $startHour = (int)$startToSleep->format("H");
                $startMinute = (int)$startToSleep->format("i");
                $endHour = (int)$wakesUp->format("H");
                $endMinute = (int)$wakesUp->format("i");

                $hour = $startHour;
                $minute = $startMinute;
                while ($hour !== $endHour || $minute !== $endMinute) {
                    $minutes[$thisGuardId][$minute] = ($minutes[$thisGuardId][$minute] ?? 0) + 1;

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
            } elseif (strpos($entry[1], 'falls') !== false) {
                $startToSleep = $entry[0];
            } elseif (strpos($entry[1], 'wakes') !== false) {
                $wakesUp = $entry[0];
                $guardsSleepTrack[$guardId] = ($guardsSleepTrack[$guardId] ?? 0) + $this->getMinutesDifference($startToSleep,
                        $wakesUp);
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
