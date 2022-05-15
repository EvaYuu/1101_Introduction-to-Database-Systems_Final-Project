<?php
    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    try{
        if(!isset($_POST['sname'])||empty($_POST['sname'])){
            echo 'FAILED';
            exit();
        }
        $sname = $_POST['sname'];
        $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("select shop_name from shops where shop_name=:shop_name");
        $stmt->execute(array('shop_name'=>$sname));

        if($stmt->rowCount()==0){
            echo 'YES';
        }
        else{
            echo 'NO';
        }
    }
    catch(Exception $e){
        echo 'FAILED';
    }
?>