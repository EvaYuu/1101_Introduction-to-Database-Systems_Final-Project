<?php
    session_start();

    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    $uacc = $_SESSION['Account']; 
    $fsta =  $_POST['filter_status'];

    if(!isset($_SESSION['Authenticated'])||$_SESSION['Authenticated']!=true){
        header("Location: index.php");
        exit();
    }

    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if($fsta=='All'){
        $stmt = $conn->prepare("select * from orders where user_account=:user_account");
        $stmt->execute(array('user_account'=>$uacc));
    }
    else{
        $stmt = $conn->prepare("select * from orders where user_account=:user_account AND status=:status");
        $stmt->execute(array('user_account'=>$uacc, 'status'=>$fsta));
    }
    $mrow = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $_SESSION['filter_result_myOrder'] = $mrow;
    header("Location: myOrder.php");
    exit();
?>