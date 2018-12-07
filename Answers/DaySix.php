<?php

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
        return $this->grid->getSafeAreaSize();
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
}
