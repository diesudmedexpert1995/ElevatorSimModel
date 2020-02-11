<?php


// Elevator class - elevator logic
class ElevatorClass
{
    private $isMoving = false;
    private $direction;
    private $RequiredFloors = [];
    private $currentFloor;
    private $totalFloors;

    public function __construct()
    {
        global $config;
        $this->totalFloors = $config['total_fls'];
    }

    /**
     * @return bool
     */
    public function isMoving(): bool
    {
        return $this->isMoving;
    }

    /**
     * @param bool $isMoving
     */
    public function setIsMoving(bool $isMoving): void
    {
        $this->isMoving = $isMoving;
    }

    /**
     * @return mixed
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @param mixed $direction
     */
    public function setDirection($direction): void
    {
        $this->direction = $direction;
    }

    /**
     * @return array
     */
    public function getRequiredFloors(): array
    {
        return $this->RequiredFloors;
    }

    /**
     * @param array $RequiredFloors
     */
    public function setRequiredFloors(array $RequiredFloors): void
    {
        $this->RequiredFloors = $RequiredFloors;
    }

    /**
     * @return mixed
     */
    public function getCurrentFloor()
    {
        return $this->currentFloor;
    }

    /**
     * @param mixed $currentFloor
     */
    public function setCurrentFloor($currentFloor): void
    {
        $this->currentFloor = $currentFloor;
    }

    /**
     * @return mixed
     */
    public function getTotalFloors()
    {
        return $this->totalFloors;
    }


    public function requestFloor($level, $dir)
    {
        $fl = new FloorRequest();
        $fl->setLevel($level);
        $fl->setDir($dir);
        $this->addRequiredFloors($fl);
    }


    public function moveToFloor($level, $dir)
    {
        $this->requestFloor($level, $dir);
    }


    public function transport($fromLevel, $toLevel)
    {

        if ($fromLevel < $toLevel) {
            $d = "up";
        } else {
            $d = "down";
        }

        $this->requestFloor($fromLevel, $d);
        $this->moveToFloor($toLevel, $d);
    }



    public function existedRequiredFloor($RequiredFloor)
    {
        foreach ($this->RequiredFloors as $fl) {
            if ($fl->dir == $RequiredFloor->dir && $fl->level == $RequiredFloor->level) {
                return true;
            }
        }

        return false;
    }


    public function addRequiredFloors($Reqfl)
    {
        if (!$this->existedRequiredFloor($Reqfl)) {
            $this->RequiredFloors[] = $Reqfl;
            $this->sortReqfls();
            $this->buildCost();
        }
    }


    public function removeRequiredFloors($Reqfl)
    {
        $fls = [];
        $total = $this->totalRequiredFloors();
        for ($i = 0; $i < $total; $i++) {
            $fl = $this->RequiredFloors[$i];
            if ($fl->dir == $Reqfl->dir && $fl->level == $Reqfl->level) {
                unset($this->RequiredFloors[$i]);
            }
        }
    }



    public function totalRequiredFloors()
    {
        return count($this->RequiredFloors);
    }



    public function hasRequiredFloors()
    {
        return $this->totalRequiredFloors() > 0;
    }


    public function switchDir()
    {
        if ($this->totalRequiredFloors() > 0) {
            $this->setDir($this->getDir() == "up" ? "down" : "up");
        } else {
            $this->isMoving = false;
            $this->direction = "stand";
        }
    }


    public function detectSwitchDir()
    {
        if ($this->currentFloor == 1) {
            $this->direction = "up";
        } else if ($this->currentFloor == $this->totalFloors) {
            $this->direction = "down";
        }
    }

    public function isUp()
    {
        return $this->getDir() == "up";
    }

    public function isDown()
    {
        return $this->getDir() == "down";
    }


    public function isStand()
    {
        if ($this->direction == "stand" && $this->totalRequiredFloors() == 0) {
            return true;
        }

        return false;
    }



    public function openDoor()
    {
        if ($this->currf){
        return true;
    }

        return false;
    }


    public function closeDoor()
    {
        if ($this->currentFloor){
        return true;
    }

        return false;
    }

    public function alarm()
    {
        return true;
    }


    public function processAtRequiredFloor($fl)
    {
        if ($this->currentFloor == $fl->level) {
            $this->openDoor();
            $this->closeDoor();
            $this->removeRequiredFloors($fl);
            $this->buildCost();
        }

        $this->isMoving = true;
        $this->direction = $fl->dir;
        $maxfl = $this->getMaxRequestflLevelBydir($this->direction);
        if ($maxfl == null) {
            $this->switchdir();
        } else {
            $this->detectSwitchDir();
        }
        if ($this->isUp()) {
            $this->currentFloor += 1;
        } else if ($this->isDown()) {
            $this->currentFloor -= 1;
        }
    }

    public function run()
    {
        if ($this->isStand()) {
            $this->isMoving = false;
            $this->direction = "stand";
            return;
        }
        $fl = $this->getMinCost();
        if ($fl == null) {
            return;
        }

        $this->processAtRequiredFloor($fl);

        $this->run();
    }


    public function getMinCost()
    {
        if ($this->currentFloor == 1 && $this->totalRequiredFloors() == 0) {
            return null;
        }
        $this->buildCost();
        $min = $this->totalFloors;
        $minfl = null;
        foreach ($this->RequiredFloors as $fl) {
            if ($fl->cost <= $min) {
                $min = $fl->cost;
                $minfl = $fl;
            }
        }
        return $minfl;
    }

    public function getMaxRequestflLevelBydir($dir)
    {
        if ($dir == "up") {
            return $this->getRequestedMaxLevel($dir);
        }

        if ($dir == "down") {
            return $this->getRequestedMinLevel($dir);
        }
    }

    public function getRequestedMaxLevel($dir)
    {
        $max = 1;
        $maxfl = null;
        foreach ($this->RequiredFloors as $fl) {
            if ($fl->dir == $dir && $fl->level > $max) {
                $max = $fl->level;
                $maxfl = $fl;
            }
        }

        return $maxfl;
    }

    public function getRequestedMinLevel($dir)
    {
        $min = $this->totalFloors;
        $minfl = null;
        foreach ($this->RequiredFloors as $fl) {
            if ($fl->dir == $dir && $fl->level <= $min) {
                $min = $fl->level;
                $minfl = $fl;
            }
        }

        return $minfl;
    }


    public function buildCost()
    {
        $total = $this->totalFloors;
        $fls = [];
        foreach ($this->RequiredFloors as $fl) {
            $fl->cost = $fl->level - $this->currentFloor;
            $fl->cost = $fl->cost < 0 ? -$fl->cost : $fl->cost;
            if ($this->isMoving && $this->direction != $fl->dir) {
                $fl->cost += $total;
            }

            $fls[] = $fl;
        }
        $this->RequiredFloors = $fls;
    }


    public function sortReqfls()
    {
        $args = ["direction", "level"];
        usort($this->RequiredFloors, function ($a, $b) use ($args) {
            $i = 0;
            $c = count($args);
            $cmp = 0;
            while ($cmp == 0 && $i < $c) {
                $cmp = strcmp($a->$args[$i], $b->$args[$i]);
                $i++;
            }
            return $cmp;
        });
    }

}