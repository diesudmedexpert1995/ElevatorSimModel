<?php


class ElevatorAPI
{

    private $elevator;

    public function __construct()
    {
        $this->elevator = new ElevatorClass();
    }

    public function request()
    {
        $level =$_GET['level'];
        $direction = $_GET['direction'];
        $this->elevator->requestFloor($level,$direction);
        echo "<p>Request for floor added.</p>";
    }

    public function send()
    {
        $level =$_GET['level'];
        $direction = $_GET['direction'];
        $this->elevator->moveToFloor($level,$direction);
        echo "<p>Request for floor sended.</p>";
    }

    public function openDoor()
    {
        $this->elevator->openDoor();
        echo "<p>Door opened.</p>";
    }

    public function closeDoor()
    {
        $this->elevator->closeDoor();
        echo "<p>Door closed.</p>";
    }

    public function alarm()
    {
        $this->elevator->alarm();
        echo "<p>Alarm.</p>";
    }


}