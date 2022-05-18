<?php
    session_start();

    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    if(!isset($_POST['shop_name'])||!isset($_POST['distance']) || !isset($_POST['lower_price'])|| !isset($_POST['higher_price'])||!isset($_POST['meal_name'])||!isset($_POST['shop_category'])){
        echo 'FAILED';
        exit();
    }
    echo 'select = '.$_POST['distance'];
    
    // $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
    // $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // $stmt = $conn->prepare("update users set location=ST_GeomFromText(:location) where account='$uacc'");
    // $stmt->execute(array('location'=>'POINT(' . $new_ulon . ' ' . $new_ulat . '))'));
    // header("Location: nav.php");
    // exit();
?>