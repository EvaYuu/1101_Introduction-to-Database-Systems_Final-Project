<?php
    session_start();

    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';
    
    $uacc = $_SESSION['Account'];
    $uwal = $_SESSION['uwal'];

    $order_shop = $_POST['shopName'];
    $Delivery_type = $_POST['Delivery_type'];
    $Delivery_fee = $_POST['Delivery_fee'];
    $order_meal = $_POST['order_meal'];
    $shop_distance = $_POST['Delivery_distance'];
    // echo $order_shop;
    // echo $Delivery_type;
    // print_r($order_meal);
    // echo $Delivery_fee;

    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
    $stmt = $conn->prepare("select owner, shop_name from shops where shop_name='$order_shop'");
    $stmt->execute();
    $shop_owner = $stmt->fetch()['owner'];

    try{
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->beginTransaction();
        $message ='';
        $isException = false;
        $total_price = $Delivery_fee;
        foreach($order_meal as $k => $v){
            if($v != 0){
                $stmt = $conn->prepare("select meal_name, quantity, shop_name, price from menus where shop_name='$order_shop' and meal_name='$k'");
                $stmt->execute();
                $meal = $stmt->fetch();
                if($stmt->rowCount()==0){// meal dosen't exist in database
                    $isException = true;
                    $message = $message.'Some of the meals have been deleted.\n';
                }
                if($v > $meal['quantity']){
                    $isException = true;
                    $message = $message.$k.'    order amount > shop quantity\n';
                }
                $total_price += $meal['price'] * $v;
            }
        }
        if($total_price > $uwal){
            $isException = true;
            $message = $message.'錢包餘額不足\n';
        }
        //final
        if($isException == true){
            throw new Exception($message);
        }
        //store order information
        $stmt = $conn->prepare("insert into orders(shop_name, user_account, status, start_time, delivery_distance, total_price, delivery_type) values(:shop_name, :user_account, 'Not Finished', now(), :delivery_distance, :total_price, :delivery_type)");
        $stmt->execute(array('shop_name'=>$order_shop, 'user_account'=>$uacc, 'delivery_distance'=>$shop_distance, 'total_price'=>$total_price, 'delivery_type'=>$Delivery_type));
        $OID = $conn->lastInsertId();
        //store order_menu information
        foreach($order_meal as $k => $v){
            if($v != 0){
                $stmt = $conn->prepare("select meal_name, price, quantity, image, image_type, shop_name from menus where shop_name='$order_shop' and meal_name='$k'");
                $stmt->execute();
                $meal = $stmt->fetch();
                $mprice = $meal['price'];
                $mimg = $meal['image'];
                $mimgt = $meal['image_type'];
                $mquan = $meal['quantity'];
                $stmt = $conn->prepare("insert into order_menus(OID, meal_name, price, order_quantity, image, image_type) values(:OID, :meal_name, :price, :order_quantity, :image, :image_type)");
                $stmt->execute(array('OID'=>$OID, 'meal_name'=>$k, 'price'=>$mprice, 'order_quantity'=>$v, 'image'=>$mimg, 'image_type'=>$mimgt));
                // storage update
                $amount_change = $mquan - $v;
                $stmt = $conn->prepare("update menus set quantity = :amount_change where shop_name='$order_shop' and meal_name='$k'");
                $stmt->execute(array('amount_change'=>$amount_change));
            }
        }

        //update tansactions
        //user->Payment
        $payment = (-1)*$total_price;
        $stmt = $conn->prepare("insert into transactions(account, action, trader, amount_change) values(:account, 'Payment', :trader, :amount_change)");
        $stmt->execute(array('account'=>$uacc, 'trader'=>$shop_owner, 'amount_change'=>$payment));
        //shop->Receive
        $receive = $total_price;
        $stmt = $conn->prepare("insert into transactions(account, action, trader, amount_change) values(:account, 'Receive', :trader, :amount_change)");
        $stmt->execute(array('account'=>$shop_owner, 'trader'=>$uacc, 'amount_change'=>$receive));
        //update balance
        //user
        $uwal -= $total_price;
        $_SESSION['uwal'] = $uwal;
        $stmt = $conn->prepare("update users set walletbalance = :walletbalance where account='$uacc'");
        $stmt->execute(array('walletbalance'=>$uwal));
        //shop owner
        $stmt = $conn->prepare("select shops.shop_name, users.account, users.walletbalance, shops.owner as owner from shops inner JOIN users on shops.owner = users.account where shop_name='$order_shop'");
        $stmt->execute();
        $shop_info = $stmt->fetch();
        $shop_bal = $shop_info['walletbalance'];
        $shop_owner = $shop_info['owner'];
        $shop_bal += $total_price;
        $stmt = $conn->prepare("update users set walletbalance=:walletbalance where account='$shop_owner'");
        $stmt->execute(array('walletbalance'=>$shop_bal));
        
        $conn->commit();
        echo <<< EOT
        <!DOCTYPE>
        <html>
            <body>
                <script>
                alert("訂購成功 你的訂單編號是：$OID");
                window.location.replace("myOrder_preview.php");
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