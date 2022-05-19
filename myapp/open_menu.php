<?php
    session_start();
    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sname = $_GET['shop_name'];

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
                    window.location.replace("nav.php");
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
</head>
<body>
<h4 class="modal-title">menu</h4>
<div class="row">
<div class=" row  col-xs-8">
    <div class="row">
    <div class="  col-xs-12">
        <table class="table" style=" margin-top: 15px;">
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">Picture</th>
            <th scope="col">meal name</th>               
            <th scope="col">price</th>
            <th scope="col">Quantity</th>                
            <th scope="col">Order check</th>
            </tr>
        </thead>
        <tbody>
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
                            <td> <input type="checkbox" id="cbox1" value="$mname"></td>
                            </tr>
                        EOT;
                    }
                }
            ?>
        </tbody>
        </table>
    </div>
    </div>
</div>
</div>
      

</body>

</html>