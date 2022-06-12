<?php
    session_start();

    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    $sname = $_SESSION['Shop_name'];
    $fsta =  $_POST['filter_status'];
    

    if(!isset($_SESSION['Authenticated'])||$_SESSION['Authenticated']!=true){
        header("Location: index.php");
        exit();
    }

    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if($fact=='All'){
        $stmt = $conn->prepare("select * from orders where shop_name=:shop_name");
        $stmt->execute(array('shop_name'=>$sname));
    }
    else{
        $stmt = $conn->prepare("select * from orders where shop_name=:shop_name AND status=:status");
        $stmt->execute(array('shop_name'=>$sname, 'status'=>$fsta));
    }
    $mrow = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $_SESSION['filter_result_shopOrder'] = $mrow;
    header("Location: shopOrder.php");
    exit();
?>