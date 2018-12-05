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
            fwrite(STDOUT, "$character: ...");
            $regexPattern = "/[$character]+/i";
            $newInput = preg_replace($regexPattern, '', $this->input);
            $chain = new Chain($newInput);
            $chain->react();
            if ($smallestChainCount === null || $smallestChainCount > $chain->length()) {
                $smallestChainCount = $chain->length();
            }
            fwrite(STDOUT, " Done! (Smallest count: $smallestChainCount)\n");
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
                $regexPattern = "/($upper$lower|$lower$upper)+/";
                $this->source = preg_replace($regexPattern, '', $this->source);
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
