<?php
    session_start();

    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    $uacc = $_SESSION['Account']; 
    $fact =  $_POST['filter_action'];

    if(!isset($_SESSION['Authenticated'])||$_SESSION['Authenticated']!=true){
        header("Location: index.php");
        exit();
    }

    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if($fact=='All'){
        $stmt = $conn->prepare("select * from transactions where account=:account");
        $stmt->execute(array('account'=>$uacc));
    }
    else{
        $stmt = $conn->prepare("select * from transactions where account=:account AND action=:action");
        $stmt->execute(array('account'=>$uacc, 'action'=>$fact));
    }
    $mrow = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $_SESSION['filter_result'] = $mrow;
    header("Location: transactionRecord.php");
    exit();
?>