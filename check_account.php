<?php
    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    try{
        if(!isset($_POST['uacc'])||empty($_POST['uacc'])){
            echo 'FAILED';
            exit();
        }
        $uacc = $_POST['uacc'];
        $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("select account from users where account=:account");
        $stmt->execute(array('account'=>$uacc));

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