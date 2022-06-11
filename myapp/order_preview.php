<?php
    session_start();
    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sname = $_POST['order_shop'];
    $Delivery = $_POST['Delivery'];
    $order_meal = $_POST['order_meal'];
    // echo 'shop name = '."$sname";
    // echo 'Delivery = '."$Delivery";
    // print_r($order_meal);
    $_SESSION['order_shop'] = $sname;

    try{
        if($sname==''){
            throw new Exception('Something error');
        }
        $stmt = $conn->prepare("select meal_name, price, quantity, image, image_type from menus where shop_name='$sname'");
        $stmt->execute();
        $mrows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $hasorder = False;
        // check meal exists and compute total price
        $subtotal = 0;
        foreach($order_meal as $k => $v){
            $stmt = $conn->prepare("select meal_name, price, quantity, image, image_type from menus where shop_name='$sname' and meal_name='$k'");
            $stmt->execute();
            $meal = $stmt->fetch();
            if($stmt->rowCount()==0){// meal dosen't exist in database
                throw new Exception('Some of the meals have been deleted. ');
            }
            if($v!=0){
                $hasorder = True;
                $subtotal += $meal['price'] * $v;
            }
        }
        if(!$hasorder){
            unset($_SESSION['order_meal']);
            throw new Exception('Have not ordered anything.');
        }
        $_SESSION['order_meal'] = $order_meal;//store ordered meals to session([meal name]->count)
        // print_r($order_meal);

    
    }
    catch(Exception $e){
        $msg = $e->getMessage();

        echo <<< EOT
            <!DOCTYPE>
            <html>
                <body>
                    <script>
                    alert("$msg");
                    window.location.replace("open_menu.php");
                    </script>
                </body>
            </html>
        EOT;
    }
?>


<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <title>Hello, world!</title>
  <script>

  </script>
</head>
<body>
<div class="container">
<h4 class="modal-title">Order</h4>

<div class="row">
<div class=" row  col-xs-8">
    <div align="right">
    <a href='open_menu.php' input type='button'>X</a>
    </div>
    <div class="row">
    <div class="row  col-xs-12">
        <table class="table" style=" margin-top: 15px;">
        <thead>
            <tr>
            <th scope="col">Picture</th>
            <th scope="col">meal name</th>               
            <th scope="col">price</th>
            <th scope="col">Order Quantity</th>                
            </tr>
        </thead>
        <tbody>
            <form class="form" action="" method='post'>
            <?php 
                if(!empty($mrows)){
                    foreach($mrows as $r){
                        $mname = $r['meal_name'];
                        $count = $order_meal["$mname"];
                        if($count!=0){
                            $mname = htmlentities($mname);
                            $price = htmlentities($r['price']);
                            $mimg = htmlentities($r['image']);
                            $mimg_type = htmlentities($r['image_type']);
                            $count = htmlentities($count);
                            echo <<< EOT
                                <tr>
                                <td><img src="data:$mimg_type;base64, $mimg" width="100" height="100" alt="$mname"></td>
                                <td>$mname</td>
                                <td>$price </td>
                                <td>$count</td>
                                <input type="hidden" name='order_meal[$mname]' value=$count>    
                                <td></td>
                                </tr>
                            EOT;
                        }
                    }
                }
                if($Delivery == "Delivery"){
                    $Del_fee = 19;
                    $total_price = $subtotal + $Del_fee;
                    $Del_fee = htmlentities($Del_fee);
                }
                else{
                    $total_price = $subtotal;
                }
                
                $subtotal = htmlentities($subtotal);
                $total_price = htmlentities($total_price);
            ?>
            <tr>
                <td></td><td></td><td></td><td></td><td></td>
                <td>
                    <div align="right">
                        Subtotal    $<?php echo $subtotal; ?></br>
                        <?php 
                            $mname = htmlentities($mname);
                            if($Delivery == "Delivery"){
                                echo <<< EOT
                                    Delivery    $$Del_fee</br>
                                EOT;
                            } 
                        ?>
                        Total Price     $<?php echo $subtotal; ?></br>
                        <form action="order_transaction.php" method="POST">
                            <input type="hidden" name="shopName" value=$sname>
                            <input type="hidden" name="totalPrice" value=$total_price>
                            <button type="submit" class="btn btn-primary">Order</button>  
                        </form>
                        <!--<a href='nav.php' class="btn btn-default" input type='button'>Order</a>-->
                    </div>
                </td>
            </tr>
            </form>
        </tbody>
        </table>
        
    </div>
    </div>
</div>
</div>
</div>
      

</body>

</html>