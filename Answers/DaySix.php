<?php

const DONT_BOTHER = -2;
const DANGEROUS = -1;
const SAFE_UNEXPLORED = 1;
const ENQUEUED = 2;
const SAFE_EXPLORED = 3;
const SAFE_DISTANCE = 10000;

class DaySix
{
    private $grid;

    public function __construct()
    {
        $this->grid = new SpaceGrid(getDaySixInputs());
    }

    public function firstStar()
    {
        return $this->grid->getLargestFiniteAreaSize();
    }

    public function secondStar()
    {
//        $this->grid->composeSafeAreaGrid();
//        $this->grid->drawSafeAreaGrid();
        return $this->grid->getSafeAreaSize();
//        return $this->grid->getLargestIslandArea();
    }
}


class SpaceGrid
{
    private $trueMinX;
    private $trueMaxX;
    private $trueMinY;
    private $trueMaxY;

    // Normalized for minX and minY = 0;
    private $maxX;
    private $maxY;
    private $coordinates;

    private $infiniteAreaCoordinatesIds;


    private $safeAreaGrid;
    private $safeAreasSizes;


    public function __construct($rawCoordinates)
    {
        $arrayX = array_column($rawCoordinates, 0);
        $arrayY = array_column($rawCoordinates, 1);

        // Get true boundaries
        $this->trueMinX = min($arrayX);
        $this->trueMaxX = max($arrayX);
        $this->trueMinY = min($arrayY);
        $this->trueMaxY = max($arrayY);
        // Normalize boundaries so the grid starts at 0, 0
        $this->maxX = $this->trueMaxX - $this->trueMinX;
        $this->maxY = $this->trueMaxY - $this->trueMinY;
        $this->coordinates = array_map(function ($coordinate) {
            return [$coordinate[0] - $this->trueMinX, $coordinate[1] - $this->trueMinY];
        }, $rawCoordinates);
    }

    public function getLargestFiniteAreaSize()
    {
        // Compose Initial Infinite Area Coordinate Blacklist
        foreach ($this->coordinates as $id => $coordinate) {
            if ($this->isOnEdge($coordinate[0], $coordinate[1])) {
                $this->infiniteAreaCoordinatesIds[] += $id;
            }
        }

        // Get finite areas
        $coordinateAreas = [];
        for ($x = 0; $x <= $this->maxX; $x++) {
            for ($y = 0; $y <= $this->maxY; $y++) {
                $closestId = $this->getClosestCoordinateToPoint($x, $y);
                if ($closestId !== -1) {
                    $coordinateAreas[] += $closestId;
                }
            }
        }

        return max(array_count_values($coordinateAreas));
    }

    protected function isOnEdge($x, $y)
    {
        return $x === 0 || $x === $this->maxX || $y === 0 || $y === $this->maxY;
    }

    protected function isInfiniteAreaCoordinate($coordinateId)
    {
        return array_search($coordinateId, $this->infiniteAreaCoordinatesIds) !== false;
    }

    protected function getClosestCoordinateToPoint($x, $y)
    {
        $minDistance = null;
        $closestCoordinateId = null;
        $count = null;
        foreach ($this->coordinates as $coordinateId => $coordinate) {
            $distance = abs($coordinate[0] - $x) + abs($coordinate[1] - $y);

            if ($minDistance === $distance) {
                $count++;
            }

            if ($minDistance === null || $minDistance > $distance) {
                $minDistance = $distance;
                $closestCoordinateId = $coordinateId;
                $count = 1;
            }
        }

        if ($count > 1 || $this->isInfiniteAreaCoordinate($closestCoordinateId)) {
            return -1;
        }

        if ($this->isOnEdge($x, $y)) {
            $this->infiniteAreaCoordinatesIds[] += $closestCoordinateId;
            return -1;
        }

        return $closestCoordinateId;
    }


    public function getSafeAreaSize()
    {
        $size = 0;
        for ($x = 0; $x <= $this->maxX; $x++) {
            for ($y = 0; $y <= $this->maxY; $y++) {
                $distance = 0;
                foreach ($this->coordinates as $coordinate) {
                    $distance += abs($coordinate[0] - $x) + abs($coordinate[1] - $y);
                }
                if ($distance < SAFE_DISTANCE) {
                    $size++;
                }
            }
        }

        return $size;
    }

    public function composeSafeAreaGrid()
    {
        for ($x = 0; $x <= $this->maxX; $x++) {
            for ($y = 0; $y <= $this->maxY; $y++) {
                $distance = 0;
                foreach ($this->coordinates as $coordinate) {
                    $distance += abs($coordinate[0] - $x) + abs($coordinate[1] - $y);
                }
                if ($distance < SAFE_DISTANCE) {
                    $this->safeAreaGrid[$x][$y] = SAFE_UNEXPLORED;
                }
            }
        }
    }

    public function drawSafeAreaGrid()
    {
        file_put_contents("out.txt", "");

        for ($y = 0; $y <= $this->maxY; $y++) {
            $row = "";
            for ($x = 0; $x <= $this->maxX; $x++) {
                $row .= (($this->safeAreaGrid[$x][$y] ?? DONT_BOTHER) === SAFE_UNEXPLORED) ? "#" : ".";
            }
            $row .= "\n";
            file_put_contents("out.txt", $row, FILE_APPEND);
        }
    }

    public function getLargestIslandArea()
    {
        // TODO: Transverse only on SAFE areas
        foreach ($this->safeAreaGrid as $x => $columns) {
            foreach ($columns as $y => $value) {
                if ($value === SAFE_UNEXPLORED) {
                    $this->exploreArea($x, $y);
                }
            }
        }

        return max($this->safeAreasSizes);
    }

    public function exploreArea($x, $y)
    {
        $this->safeAreaGrid[$x][$y] = SAFE_EXPLORED;

        $areaSize = 1;
        $toVisit = []; // TODO: Use Queue for optimization
        $queuePointer = 0;

        for ($dX = $x - 1; $dX <= $x + 1; $dX++) {
            for ($dY = $y - 1; $dY <= $y + 1; $dY++) {
                if ($dX < 0 || $dY < 0 || empty($this->safeAreaGrid[$dX][$dY])) {
                    continue;
                }
                if ($this->safeAreaGrid[$dX][$dY] === SAFE_UNEXPLORED) {
                    array_push($toVisit, [$dX, $dY]);
                    $this->safeAreaGrid[$dX][$dY] = ENQUEUED;
                }
            }
        }

        for ($queuePointer = 0; $queuePointer < count($toVisit); $queuePointer++) {
            $x = $toVisit[$queuePointer][0];
            $y = $toVisit[$queuePointer][1];
            $this->safeAreaGrid[$x][$y] = SAFE_EXPLORED;
            $areaSize++;

            for ($dX = $x - 1; $dX <= $x + 1; $dX++) {
                for ($dY = $y - 1; $dY <= $y + 1; $dY++) {
                    if ($dX < 0 || $dY < 0 || empty($this->safeAreaGrid[$dX][$dY])) {
                        continue;
                    }

                    if ($this->safeAreaGrid[$dX][$dY] === SAFE_UNEXPLORED) {
                        array_push($toVisit, [$dX, $dY]);
                        $this->safeAreaGrid[$dX][$dY] = ENQUEUED;

                        if (count($toVisit) > 5000) {
                            $toVisit = array_splice($toVisit, 0, $queuePointer);
                            $queuePointer = 0;
                        }
                    }
                }
            }
        }

        $this->safeAreasSizes[] += $areaSize;
    }
}

// 14783
