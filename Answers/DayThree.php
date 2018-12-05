<?php

class DayThree
{
    private $input;
    private $canvas;
    const MAX_X = 1000;
    const MAX_Y = 1000;

    public function __construct()
    {
        $this->input = getDayThreeInputs();
    }

    public function firstStar()
    {
        $this->cleanCanvas();
        foreach ($this->input as $instruction) {
            $this->draw($instruction);
        }
        return $this->countRepetitions();
    }

    public function secondStar()
    {
        $this->cleanCanvas();
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

    protected function cleanCanvas()
    {
        for ($i = 0; $i < self::MAX_X; $i++) {
            for ($j = 0; $j < self::MAX_Y; $j++) {
                $this->canvas[$i][$j] = 0;
            }
        }
    }
}
