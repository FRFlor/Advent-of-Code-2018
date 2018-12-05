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
                $greatestSleep = $sleepTime;
                $greatestSleeper = $guardId;
            }
        }

        // Find the minute most slept by the greatest sleeper
        $minutesArray = $this->getSleepDistributionForGuards($greatestSleeper);
        $highestCount = 0;
        $mostSleptMinute = null;
        foreach ($minutesArray[$greatestSleeper] as $minute => $count) {
            if ($count > $highestCount) {
                $highestCount = $count;
                $mostSleptMinute = $minute;
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
                    $highestCount = $count;
                    $mostSleptMinute = $minute;
                    $sleepingGuard = $guardId;
                }
            }
        }

        return $sleepingGuard * $mostSleptMinute;
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
        $startsToSleep = null;
        $wakesUp = null;
        $thisGuardId = null;

        $isRightGuard = false;
        foreach ($this->guardsSchedule as $entry) {
            if ($this->isStartOfShift($entry)) {
                preg_match("/Guard #(\d+) .+/", $entry[1], $matches);
                $thisGuardId = (int)$matches[1];
                $isRightGuard = ($targetGuardId === null || $thisGuardId === $targetGuardId);

            } elseif ($isRightGuard && $this->isStartOfSleep($entry)) {
                $startsToSleep = $entry[0];

            } elseif ($isRightGuard && $this->isStartOfWakingUp($entry)) {
                $minutes[$thisGuardId] = $minutes[$thisGuardId] ?? [];
                $wakesUp = $entry[0];
                $clock = new Clock($startsToSleep, $wakesUp);

                while (! $clock->isTimeUp()) {
                    $minute = $clock->getMinute();
                    $minutes[$thisGuardId][$minute] = ($minutes[$thisGuardId][$minute] ?? 0) + 1;
                    $clock->increment();
                }
            }
        }

        return $minutes;
    }

    protected function getMinutesSleptByEachGuard()
    {
        $guardId = null;
        $startsToSleep = null;
        $wakesUp = null;

        $guardsSleepTrack = [];
        foreach ($this->guardsSchedule as $entry) {
            if ($this->isStartOfShift($entry)) {
                preg_match("/Guard #(\d+) .+/", $entry[1], $matches);
                $guardId = $matches[1];

            } elseif ($this->isStartOfSleep($entry)) {
                $startsToSleep = $entry[0];

            } elseif ($this->isStartOfWakingUp($entry)) {
                $wakesUp = $entry[0];
                $guardsSleepTrack[$guardId] =
                    ($guardsSleepTrack[$guardId] ?? 0) + Clock::getMinutesDifference($startsToSleep, $wakesUp);
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

    protected function isStartOfShift($event)
    {
        return strpos($event[1], 'Guard') !== false;
    }

    protected function isStartOfSleep($event)
    {
        return strpos($event[1], 'falls') !== false;
    }

    protected function isStartOfWakingUp($event)
    {
        return strpos($event[1], 'wakes') !== false;
    }
}

class Clock
{
    private $minute;
    private $hour;
    private $endHour;
    private $endMinute;

    public function __construct($startTime, $endTime)
    {
        $this->hour = (int)$startTime->format("H");
        $this->minute = (int)$startTime->format("i");
        $this->endHour = (int)$endTime->format("H");
        $this->endMinute = (int)$endTime->format("i");
    }

    public function increment()
    {
        $this->minute++;

        if ($this->minute > 59) {
            $this->hour++;
            $this->minute = 0;

            if ($this->hour > 23) {
                $this->hour = 0;
            }
        }
    }

    public function isTimeUp()
    {
        return $this->endHour === $this->hour && $this->endMinute === $this->minute;
    }

    public function getMinute()
    {
        return $this->minute;
    }

    public static function getMinutesDifference($date1, $date2)
    {
        $interval = $date1->diff($date2);

        return $interval->h * 60 + $interval->i;
    }
}
