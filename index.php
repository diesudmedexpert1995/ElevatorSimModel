<?php

require_once ("api/config/config.php");

$api = new ElevatorAPI();
if (isset($_GET['q'])){
    $action = $_GET['q'];
} else {
    echo "<p>Please type get param</p>";
    exit();
}

switch ($action){
    case 'request':
        $api->request();
        break;
    case 'send':
        $api->send();
        break;
    case 'open_door':
        $api->openDoor();
        break;
    case 'close_door':
        $api->closeDoor();
        break;
    case 'alarm':
        $api->alarm();
        break;
}