<?php

class DayFive
{
    private $input;

    public function __construct()
    {
        $this->input = getDayFiveInputs();
    }

    public function firstStar()
    {
        $chain = new Chain($this->input);

        $chain->react();

        return $chain->length();
    }

    public function secondStar()
    {
        $characters = array_unique(str_split(strtoupper($this->input)));
        sort($characters);

        $smallestChainCount = null;

        foreach ($characters as  $character) {
            $newInput = preg_replace("/[$character]+/i", '', $this->input);
            $chain = new Chain($newInput);
            $chain->react();
            if ($smallestChainCount === null || $smallestChainCount > $chain->length()) {
                $smallestChainCount = $chain->length();
            }
        }

        return $smallestChainCount;
    }
}


class Chain
{
    private $source;
    private $upperChars;

    public function __construct(string $source)
    {
        $this->source = $source;
        $this->upperChars = array_unique(str_split(strtoupper(getDayFiveInputs())));
    }

    public function react()
    {
        $previousLength = strlen($this->source);
        while(true)
        {
            foreach ($this->upperChars as $upper) {
                $lower = strtolower($upper);
                // Obs: The regex OR operator has very low execution speed.
                // Two chained preg_replace calls executes much faster than one with OR operator
                $this->source = preg_replace("/($upper$lower)+/", '', $this->source);
                $this->source = preg_replace("/($lower$upper)+/", '', $this->source);
            }
            $newLength = strlen($this->source);

            if ($previousLength === $newLength) {
                break;
            }

            $previousLength = $newLength;
        }
    }

    public function length()
    {
        return strlen($this->source);
    }
}
