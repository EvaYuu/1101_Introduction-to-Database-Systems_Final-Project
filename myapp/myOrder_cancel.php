<?php
    session_start();

    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try{
        if(!isset($_GET['OID'])){
            throw new Exception('Something error');
        }
        $OID = $_GET['OID'];
        
        //get user(account)&shop(trader)info
        $stmt = $conn->prepare("select * from orders where OID=:OID");
        $stmt->execute(array('OID'=>$OID));
        $row = $stmt->fetch();
        $buyer = $row['user_account'];
        $shop_name = $row['shop_name'];
        $money = $row['total_price'];
        //get user account of the shop(trader)
        $stmt = $conn->prepare("select * from shops where shop_name=:shop_name");
        $stmt->execute(array('shop_name'=>$shop_name));
        $row = $stmt->fetch();
        $seller = $row['owner']; 

        $conn->beginTransaction();
        //update order status (Not finished->Cancel)
        $stmt = $conn->prepare("update orders set status='Cancel' where OID:=OID");
        $stmt->execute(array('OID'=>$OID));
        //refund
        //user->Receive
        $stmt = $conn->prepare("insert into transactions(account, action, trader, amount_change) values(:account, 'Receive', :trader, :amount_change)");
        $stmt->execute(array('account'=>$buyer, 'trader'=>$shop_name, 'amount_change'=>$money));
        //shop->Payment
        $money = (-1)*$money;
        $stmt = $conn->prepare("insert into transactions(account, action, trader, amount_change) values(:account, 'Payment', :trader, :amount_change)");
        $stmt->execute(array('account'=>$seller, 'trader'=>$buyer, 'amount_change'=>$money));
        $conn->commit();

        throw new Exception('Cancel SUCCESS');
        exit();
    }
    catch(Exception $e){
        if($conn->inTransaction()){
            $conn->rollBack();
            $msg='Cancel FAIL';
        }
        $msg = $e->getMessage();
        echo <<< EOT
            <!DOCTYPE>
            <html>
                <body>
                    <script>
                    alert("$msg");
                    window.location.replace("myOrder.php");
                    </script>
                </body>
            </html>
        EOT;

    }
?>