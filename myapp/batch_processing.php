<?php
    session_start();

    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $action_string = $_REQUEST['action'];
    $page_string = $_REQUEST['page'];

    try{
        if(!isset($_GET['OID'])){
            throw new Exception('Something error');
        }
        if(!isset($_SESSION['Authenticated'])||$_SESSION['Authenticated']!=true){
            header("Location: index.php");
            exit();
        }
        $OID = $_GET['OID'];

        if($action_string == 'Finished selected orders'){//Finished selected orders
            $conn->beginTransaction();
            //update order status (Not Finished->Finished)
            $stmt = $conn->prepare("update orders set status='Finished' where OID=:OID");
            $stmt->execute(array('OID'=>$OID));
            //write end_time
            $stmt = $conn->prepare("update orders set end_time=now() where OID=:OID");
            $stmt->execute(array('OID'=>$OID));
            $conn->commit();

            echo <<< EOT
                <!DOCTYPE>
                <html>
                    <body>
                        <script>
                        alert('Done SUCCESS');
                        window.location.replace($page_string);
                        </script>
                    </body>
                </html>
            EOT;
            exit();
        }
        else{//Cancel selected orders
            //check status
            $stmt = $conn->prepare("select * from orders where OID=:OID");
            $stmt->execute(array('OID'=>$OID));
            $row = $stmt->fetch();
            $status = $row['status'];
            if($status=='Finished'){
                throw new Exception('The order has been finished!');
            }
            else if($status=='Cancel'){
                throw new Exception('The order has been canceled by the shop!');
            }
            //get user(account) & shop(trader) info
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
            //update order status (Not Finished->Cancel)
            $stmt = $conn->prepare("update orders set status='Cancel' where OID=:OID");
            $stmt->execute(array('OID'=>$OID));
            //refund
            //user->Receive 1.transaction 2.walletbalance
            $stmt = $conn->prepare("insert into transactions(account, action, trader, amount_change) values(:account, 'Receive', :trader, :amount_change)");
            $stmt->execute(array('account'=>$buyer, 'trader'=>$shop_name, 'amount_change'=>$money));
            $stmt = $conn->prepare("update users set walletbalance=walletbalance+(:money) where account=:account");
            $stmt->execute(array('account'=>$buyer, 'money'=>$money));
            //shop->Payment 1.transaction 2.walletbalance
            $money = (-1)*$money;
            $stmt = $conn->prepare("insert into transactions(account, action, trader, amount_change) values(:account, 'Payment', :trader, :amount_change)");
            $stmt->execute(array('account'=>$seller, 'trader'=>$buyer, 'amount_change'=>$money));
            $stmt = $conn->prepare("update users set walletbalance=walletbalance+(:money) where account=:account");
            $stmt->execute(array('account'=>$seller, 'money'=>$money));
            //turn back foods quantity
            $stmt = $conn->prepare("select * from order_menus where OID=:OID");
            $stmt->execute(array('OID'=>$OID));
            $mrow = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($mrow as $row) {
                $meal_name = htmlentities($row['meal_name']);
                $order_quantity = htmlentities($row['order_quantity']);
                $stmt = $conn->prepare("update menus set quantity=quantity+(:order_quantity) where meal_name=:meal_name AND shop_name=:shop_name");
                $stmt->execute(array('order_quantity'=>$order_quantity, 'meal_name'=>$meal_name, 'shop_name'=>$shop_name));
            }

            $conn->commit();

            echo <<< EOT
                <!DOCTYPE>
                <html>
                    <body>
                        <script>
                        alert('Cancel SUCCESS');
                        window.location.replace($page_string);
                        </script>
                    </body>
                </html>
            EOT;
            exit();
        }
        
    }
    catch(Exception $e){
        $msg = $e->getMessage();
        if($conn->inTransaction()){
            $conn->rollBack();
            if($action_string == 'Finished selected orders'){   //Finished selected orders
                $msg = $msg.'\nDone FAIL';
            }
            else{                                               //Cancel selected orders
                $msg = $msg.'\nCancel FAIL';
            }
        }
        echo <<< EOT
            <!DOCTYPE>
            <html>
                <body>
                    <script>
                    alert("$msg");
                    window.location.replace($page_string);
                    </script>
                </body>
            </html>
        EOT;

    }
?>