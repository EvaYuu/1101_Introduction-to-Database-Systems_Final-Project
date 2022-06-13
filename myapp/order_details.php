<?php
    session_start();
    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    try{
        if(!isset($_GET['OID']) || !isset($_GET['page'])){
            throw new Exception('Something error');
        }
        if(!isset($_SESSION['Authenticated'])||$_SESSION['Authenticated']!=true){
            header("Location: index.php");
            exit();
        }
        $OID = $_GET['OID'];
        $page = $_GET['page'].'.php';

        $stmt = $conn->prepare("select OID, meal_name, price, order_quantity, image, image_type from order_menus where OID='$OID'");
        $stmt->execute();
        $mrows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    }
    catch(Exception $e){
        $msg = $e->getMessage();

        echo <<< EOT
            <!DOCTYPE>
            <html>
                <body>
                    <script>
                    alert("$msg");
                    window.location.replace("$page");
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
    <a href="<?php echo $page;?>" input type='button'>X</a>
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
            <?php
                //select OID, meal_name, price, order_quantity, image, image_type from order_menus where OID='$OID'
                $subtotal = 0; 
                if(!empty($mrows)){
                    foreach($mrows as $r){
                        $count = $r['order_quantity'];
                        $price = $r['price'];
                        $subtotal += $count * $price;

                        $mname = htmlentities($r['meal_name']);
                        $price = htmlentities($r['price']);
                        $mimg = htmlentities($r['image']);
                        $mimg_type = htmlentities($r['image_type']);
                        $count = htmlentities($r['order_quantity']);
                        echo <<< EOT
                            <tr>
                            <td><img src="data:$mimg_type;base64, $mimg" width="100" height="100" alt="$mname"></td>
                            <td>$mname</td>
                            <td>$price </td>
                            <td>$count</td>
                            </tr>
                        EOT;
                    }
                }

                $stmt = $conn->prepare("select OID, delivery_distance, total_price, delivery_type from orders where OID='$OID'");
                $stmt->execute();
                $o = $stmt->fetch();

                $Del_fee = 0;
                $shop_distance = $o['delivery_distance'];
                $Delivery = $o['delivery_type'];
                $total_price = $o['total_price'];


                if($Delivery == "Delivery"){
                    $Del_fee = round($shop_distance/100);   // meter distance, fee = 10 times km distance
                    if($Del_fee < 10){  // minimum delivery fee is $10
                        $Del_fee = 10;
                    }
                    $Del_fee = htmlentities($Del_fee);
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
                                    Delivery fee    $$Del_fee</br>
                                EOT;
                            } 
                        ?>
                        Total Price     $<?php echo $total_price; ?></br> 
                    </div>
                </td>
            </tr>
        </tbody>
        </table>
        
    </div>
    </div>
</div>
</div>
</div>
      

</body>

</html>