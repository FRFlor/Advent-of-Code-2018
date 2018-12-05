<?php

class Timer {
    private $startTime;

    public function start()
    {
        $this->startTime = microtime(true);
    }

    public function restart()
    {
        $this->start();
    }

    public function elapsed()
    {
        return number_format(microtime(true) - $this->startTime, 2);
    }
}
