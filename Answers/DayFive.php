<?php

class DayFive
{
    private $chain;

    public function __construct()
    {
        $this->chain = str_split(getDayFiveInputs());
        array_push($this->chain, NULL);
    }

    public function firstStar()
    {
        $i = 0;
        $reactionsCount = 0;
        do {
            $reactionsInLoop = 0;
            while ($this->chain[$i+1] !== NULL) {
                if ($this->areCharsReactive($this->chain[$i], $this->chain[$i + 1])) {
                    $this->reactAt($i);
                    $reactionsInLoop++;
                } else {
                    $i++;
                }
            }
            $i = 0;
            $reactionsCount += $reactionsInLoop;
        } while($reactionsInLoop !== 0);

        return count($this->chain) - 1;
    }

    public function secondStar()
    {
    }

    // Returns true if $a and $b react with each other
    public function areCharsReactive($a, $b)
    {
        return abs(ord($a) - ord($b)) === 32;
    }

    public function reactAt($index)
    {
        array_splice($this->chain, $index, 2);
    }

}

