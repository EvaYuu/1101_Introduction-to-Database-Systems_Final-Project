<?php
    session_start();
    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if(isset($_SESSION['order_shop'])){//close window from preview_order
        $sname = $_SESSION['order_shop'];
        if(!isset($_SESSION['order_meal'])){//something wrong with ordered meals, unset session
            $hasorder = 0;
        }
        else{
            $hasorder = 1;
            //print_r($_SESSION['order_meal']);
            unset($_SESSION['order_meal']);
        }
        
    }
    else{//from home click open menu
        $sname = $_GET['order_shop'];
        unset($_SESSION['order_meal']);
        $hasorder = 0;
    }
    // echo 'type = '.gettype($_SESSION['order_meal']);//array

    try{
        if($sname==''){
            throw new Exception('Something error');
        }
        $stmt = $conn->prepare("select meal_name, price, quantity, image, image_type from menus where shop_name='$sname'");
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
            var order_count = "order_count_" + index;
            var count=document.getElementById(count_name).innerHTML;
            document.getElementById(count_name).innerHTML=parseInt(count)+1;
            document.getElementById(order_count).value = document.getElementById(count_name).innerHTML;
        }
        function dec(index){
            var count_name = "count_" + index;
            var count=document.getElementById(count_name).innerHTML;
            var order_count = "order_count_" + index;
            if(count != 0){
                document.getElementById(count_name).innerHTML=parseInt(count)-1;
            } 
            document.getElementById(order_count).value = document.getElementById(count_name).innerHTML;
        }
        
        var hasorder = <?php echo $hasorder; ?>;
        if(hasorder==1){
            //should make place the ordered list, need bug fixed.
        }

    </script>
</head>
<body>
<div class="container">
<h4 class="modal-title">menu</h4>

<div class="row">
<div class=" row  col-xs-8">
    <div align="right">
    <a href='nav.php' input type='button'>X</a>
    </div>
    <div class="row">
    <div class="row  col-xs-12">
        <table class="table" style=" margin-top: 15px;">
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">Picture</th>
            <th scope="col">meal name</th>               
            <th scope="col">price</th>
            <th scope="col">Quantity</th>                
            <th scope="col">Order</th>
            </tr>
        </thead>
        <tbody>
            <form class="form" action="order_preview.php" method='post'>
            <?php 
                if(!empty($mrows)){
                    $index = 0;
                    foreach($mrows as $r){
                        $index += 1;
                        $mname = htmlentities($r['meal_name']);
                        $price = htmlentities($r['price']);
                        $qan = htmlentities($r['quantity']);
                        $mimg = htmlentities($r['image']);
                        $mimg_type = htmlentities($r['image_type']);
                        echo <<< EOT
                            <tr>
                            <th scope="row">$index</th>
                            <td><img src="data:$mimg_type;base64, $mimg" width="100" height="100" alt="$mname"></td>
                            <td>$mname</td>
                            <td>$price </td>
                            <td>$qan </td>    
                            <td>
                            <button type="button" class="btn btn-light btn-sm " onclick="dec($index)">-</button>
                            <button type="button" class="btn btn-sm" id="count_$index">0</button>
                            <input type="hidden" id="order_count_$index" name='order_meal[$mname]' value=0>
                            <button type="button" class="btn btn-light btn-sm " onclick="insc($index)">+</button>
                            </td>
                            </tr>
                        EOT;
                    }
                }
                
            ?>
            
            <tr>
            <td></td>
            <td>
            <label class="control-label col-sm-3">Type</label>
            <div class="col-sm-8">
            <select class="form-control" placeholder="Delivery" name="Delivery">
                <option value="Delivery">Delivery</option>
                <option value="Pick-up">Pick-up</option>
            </select>
            </div></td>
            </tr>

            <tr>
                <td></td><td></td><td></td><td></td><td></td>
                <td>
                    <div align="right">
                        <input type="hidden" name="order_shop" value="<?php echo $sname; ?>">
                        <button type='submit' class="btn btn-default">Calculate the price</button>
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