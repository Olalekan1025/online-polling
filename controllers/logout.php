<?php

session_start();

include("db_connect.php");
include("globalFunctions.php");

date_default_timezone_set("Africa/Lagos");
$date = date("j-m-Y, g:i a");


if (isset($_SESSION["hostID"])) {

    $hostID = $_SESSION["hostID"];
    $onlineStatus = '0';
    $serverAddress = $_SERVER['REMOTE_ADDR'];
    $stmt = $conn->prepare("UPDATE users SET `lastAccess`=now(), `onlineStatus`=?, `lastDeviceIP`=? WHERE `hostID`=?");
    $stmt->bind_param("sss", $onlineStatus, $serverAddress, $hostID);
    $stmt->execute() or die($stmt->error);
    $stmt->close();

    if ($stmt) {

        // Take Activity Log
        $userId = $hostID;
        $eventType = "logout";
        $eventDescription = $_SESSION["hostEmail"] . " logged out successfully";
        $status = "success";
        $errorMessage = null;
        $logActivity = insertLogEvent($conn, $userId, $eventType, $eventDescription, $status, $errorMessage);

        $sessions = array('hostID', 'hostEmail', 'hostRole', 'portalAccess');
        foreach ($sessions as $session) {
            if (isset($_SESSION[$session])) {
                unset($_SESSION[$session]);
            }
        }

        //GET PREVIOUS PAGE INTO SESSION
        isset($_SESSION['previousPage']);
        header("location:.././");
    }
}
header("location:.././");
