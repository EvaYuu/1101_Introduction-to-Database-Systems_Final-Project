<?php
    session_start();

    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    //$sname = $_SESSION['Shop_name'];
    $uacc = $_SESSION['Account']; 
    $fsta =  $_POST['filter_status'];
    
    if(!isset($_SESSION['Authenticated'])||$_SESSION['Authenticated']!=true){
        header("Location: index.php");
        exit();
    }

    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //get shop name
    $stmt = $conn->prepare("select * from shops where owner=:owner");
    $stmt->execute(array('owner'=>$uacc));
    $row = $stmt->fetch();
    $shop_name = $row['shop_name'];
    //filter
    if($fsta=='All'){
        $stmt = $conn->prepare("select * from orders where shop_name=:shop_name");
        $stmt->execute(array('shop_name'=>$shop_name));
    }
    else{
        $stmt = $conn->prepare("select * from orders where shop_name=:shop_name AND status=:status");
        $stmt->execute(array('shop_name'=>$shop_name, 'status'=>$fsta));
    }
    $mrow = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $_SESSION['filter_result_shopOrder'] = $mrow;
    header("Location: shopOrder.php");
    exit();
?>