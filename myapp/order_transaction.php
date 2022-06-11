<?php
    session_start();

    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';
    
    $uacc = $_SESSION['Account'];
    $uwal = $_SESSION['uwal'];
    $mo_totalPrice = $_POST['totalPrice'];
    $mo_shopName = $_POST['shopName'];

    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);

    try{
        $message ='';
        $isException = false;
        if(!isset($mo_totalPrice)){
            header("Location: nav.php");
            exit();
        }
        //欄位空白
        if(empty($mo_totalPrice) || $mo_totalPrice==''){
            $isException = true;
            $message = $message.'欄位空白\n';
        }
        //非正整數
        if(!empty($mo_totalPrice) && (!is_numeric($mo_totalPrice)) || strval($mo_totalPrice)<=0){
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
        //update tansactions
        //user->Payment
        $payment = (-1)*$mo_totalPrice;
        $stmt = $conn->prepare("insert into transactions(account, action, trader, amount_change) values(:account, 'Payment', :trader, :amount_change)");
        $stmt->execute(array('account'=>$uacc, 'trader'=>$mo_shopName, 'amount_change'=>$payment));
        //shop->Receive
        $receive = $mo_totalPrice;
        $stmt = $conn->prepare("insert into transactions(account, action, trader, amount_change) values(:account, 'Receive', :trader, :amount_change)");
        $stmt->execute(array('account'=>$mo_shopName, 'trader'=>$uacc, 'amount_change'=>$receive));
        $conn->commit();
        echo <<< EOT
        <!DOCTYPE>
        <html>
            <body>
                <script>
                alert("訂購成功");
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
            $msg="訂購失敗";
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