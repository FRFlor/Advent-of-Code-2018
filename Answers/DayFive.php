<?php

class DayFive
{
    private $chain;

    public function __construct()
    {
        $this->chain = new Chain(getDayFiveInputs());
    }

    public function firstStar()
    {
        do {
            $reactionsInLoop = $this->chain->reactiveTransverse();
        } while ($reactionsInLoop !== 0);

        return $this->chain->count();
    }
}


class Unit {
    public $value;
    public $next;
    public $prev;

    public function __construct($char)
    {
        $this->value = ord($char);
        $this->next = null;
        $this->prev = null;
    }

    public function reactsWithNext()
    {
        return abs($this->next->value - $this->value) === 32;
    }
}

class Chain {
    private $start;
    private $end;
    private $count;

    public function __construct(string $source)
    {
        $this->count = 0;

        $this->attachFirst($source[0]);

        for ($i = 1; $i < strlen($source); $i++){
            $this->attach($source[$i]);
        }
    }

    public function attachFirst(string $char)
    {
        $firstUnit = new Unit($char);
        $this->start = $firstUnit;
        $this->end = $firstUnit;
        $this->count++;
    }

    public function attach(string $char)
    {
        $unit = new Unit($char);
        $unit->prev = $this->end;
        $this->end->next = $unit;
        $this->end = $unit;
        $this->count++;
    }

    public function detach(Unit $unit)
    {
        $previousUnit = $unit->prev;
        $nextUnit = $unit->next;

        // If the unit was the first in the chain
        if ($previousUnit === null) {
            $nextUnit->prev = null;
        }
        // If the unit was the last in the chain
        elseif ($nextUnit === null) {
            $previousUnit->next = null;
        }
        // If the unit was not in one of the extremes
        else {
            $previousUnit->next = $nextUnit;
            $nextUnit->prev = $previousUnit;
        }

        unset($unit);
        $this->count--;
    }

    public function reactiveTransverse()
    {
        $reactionsCount = 0;
        $currentUnit = $this->start;

        do {
            if ($currentUnit->reactsWithNext()) {
                $nextTarget = $currentUnit->next->next;
                $this->detach($currentUnit->next);
                $this->detach($currentUnit);

                $reactionsCount++;
            } else {
                $nextTarget = $currentUnit->next;
            }

            $currentUnit = $nextTarget;
        } while ($currentUnit->next !== null);

        return $reactionsCount;
    }

    public function count()
    {
        return $this->count;
    }
}
