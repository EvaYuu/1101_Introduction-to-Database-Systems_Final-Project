<?php
    session_start();

    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';
    
    $uacc = $_SESSION['Account'];
    $uwal = $_SESSION['uwal'];
    $add_value = $_POST['add_value'];

    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);

    try{
        $message ='';
        $isException = false;
        if(!isset($add_value)){
            header("Location: nav.php");
            exit();
        }
        //欄位空白
        if(empty($add_value) || $add_value==''){
            $isException = true;
            $message = $message.'欄位空白\n';
        }
        //非正整數
        if(!empty($add_value) && (!is_numeric($add_value)) || strval($add_value)<=0){
            $isException = true;
            if($add_value!='')
                $message = $message.'非正整數\n';
        }
        //final
        if($isException == true){
            throw new Exception($message);
        }
        
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->beginTransaction();
        $stmt = $conn->prepare("update users set walletbalance = (:original)+(:add_value) where account=:account");
        $stmt->execute(array('original'=>$uwal, 'add_value'=>$add_value, 'account'=>$uacc));
        $conn->commit();
        echo <<< EOT
        <!DOCTYPE>
        <html>
            <body>
                <script>
                alert("wallet balance update success!!");
                window.location.replace("nav.php");
                </script>
            </body>
        </html>
        EOT;
        exit();
    }
    catch(Exception $e){
        if($conn->inTransaction()){
            $conn->rollBack();
        }
        $msg = $e->getMessage();
        echo <<< EOT
            <!DOCTYPE>
            <html>
                <body>
                    <script>
                    alert("$msg");
                    window.location.replace("nav.php");
                    </script>
                </body>
            </html>
        EOT;
    }
?>