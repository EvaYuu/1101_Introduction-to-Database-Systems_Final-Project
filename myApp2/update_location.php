<?php
    session_start();

    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    if(!isset($_POST['new_ulat'])||empty($_POST['new_ulat']) || !isset($_POST['new_ulon'])||empty($_POST['new_ulon'])){
        echo 'FAILED';
        exit();
    }
    
    $uacc = $_SESSION['Account'];
    $new_ulat = $_POST['new_ulat'];
    $new_ulon = $_POST['new_ulon'];
    
    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("update users set latitude=$new_ulat, longitude=$new_ulon where account='$uacc'");
    $stmt->execute();
    header("Location: nav.php");
    exit();
?>