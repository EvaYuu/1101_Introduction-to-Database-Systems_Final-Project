<?php
    session_start();
    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sname = $_POST['shop_name'];
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
        if(isset($mrows)){
            foreach($mrows as $r){
                $mname = $r['meal_name'];
                if($order_meal["$mname"]!=0){
                    $hasorder = True;
                }
            }
        }
        if(!$hasorder){
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
      function insc(index){
          var count_name = "count_" + index;
          var count=document.getElementById(count_name).innerHTML;
          document.getElementById(count_name).innerHTML=parseInt(count)+1;
      }
      function dec(index){
          var count_name = "count_" + index;
          var count=document.getElementById(count_name).innerHTML;
          if(count != 0){
            document.getElementById(count_name).innerHTML=parseInt(count)-1;
          } 
      }
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
                                <td></td>
                                </tr>
                            EOT;
                        }
                    }
                }
                
            ?>
            <tr>
                <td></td><td></td><td></td><td></td><td></td>
                <td>
                    <div align="right">
                        Subtotal    $200</br>
                        Total Price     $300</br>
                        <a href='nav.php' class="btn btn-default" input type='button'>Order</a>
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