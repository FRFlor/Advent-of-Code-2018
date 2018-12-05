<?php

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
