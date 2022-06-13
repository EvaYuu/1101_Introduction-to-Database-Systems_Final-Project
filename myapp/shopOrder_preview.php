<?php
    session_start();

    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    $uacc = $_SESSION['Account']; 

    try{
        if(!isset($_SESSION['Authenticated'])||$_SESSION['Authenticated']!=true){
            header("Location: index.php");
            exit();
        }
    }
    catch(Exception $e){
        $msg = $e->getMessage();
        session_unset();
        session_destroy();
        echo <<< EOT
            <!DOCTYPE>
            <html>
                <body>
                    <script>
                    alert("Internal Error. $msg");
                    window.location.replace("index.php");
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
  <style>
  table td{
    white-space: nowrap;
  }
  </style>

</head>

<body>
 
  <nav class="navbar navbar-inverse">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand " href="nav.php">WebSiteName</a>
      </div>
    </div>
  </nav>
  <div class="container">

    <ul class="nav nav-tabs">
      <li><a href="nav.php">Home</a></li>
      <li><a href="shop.php">Shop</a></li>
      <li><a href="myOrder_preview.php">MyOrder</a></li>
      <li class="active"><a href="shopOrder_preview.php">Shop Order</a></li>
      <li><a href="transactionRecord_preview.php">Transaction Record</a></li>
      <li><a href="Logout.php">Log out</a></li>
    </ul>

    <div class="tab-content">
        <div class=" row  col-xs-8">

          <form class="form-horizontal" action="shopOrder_filter.php" method='post'>
            <div class="form-group"><br>
              <label class="control-label col-sm-1" for="status">Status</label>
              <div class="col-sm-5">
                <select class="form-control" id="filter" name="filter_status" onchange="this.form.submit()">
                  <option>-- Select --</option>
                  <option selected="selected">All</option>
                  <option>Finished</option>
                  <option>Not Finished</option>
                  <option>Cancel</option>
                </select>
                
              </div>
          </form>

        </div>

        <!---------------------------分隔線------------------------------>
        <div class="row">
          <div class="  col-xs-8">
            <table class="table" style=" margin-top: 15px;">
              <thead>
                <tr>
                  <th scope="col">Order ID</th>
                  <th scope="col">Status</th>
                  <th scope="col">Start</th>
                  <th scope="col">End</th>
                  <th scope="col">Shop name</th>
                  <th scope="col">Total Price</th>
                  <th scope="col">Order Details</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody>                
                  <?php
                    //TODO: filtered information
                    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    //get shop name
                    $stmt = $conn->prepare("select * from shops where owner=:owner");
                    $stmt->execute(array('owner'=>$uacc));
                    $row = $stmt->fetch();
                    $shop_name = $row['shop_name'];
                    $stmt = $conn->prepare("select * from orders where shop_name=:shop_name");
                    $stmt->execute(array('shop_name'=>$shop_name));
                    $mrow = $stmt->fetchAll(PDO::FETCH_ASSOC);   

                    foreach ($mrow as $row) {
                        $OID = htmlentities($row['OID']);
                        $status = htmlentities($row['status']);
                        $start_time = htmlentities($row['start_time']);
                        $end_time = htmlentities($row['end_time']);
                        $shop_name = htmlentities($row['shop_name']);
                        $total_price = htmlentities($row['total_price']);
                        
                        echo <<< EOT
                            <tr>
                            <th scope="row">$OID</th>
                            <td>$status</td>
                            <td>$start_time</td>
                            <td>$end_time</td>
                            <td>$shop_name</td>
                            <td>$total_price</td>
                            <td><button type="button" class="btn btn-info" onclick="javascript:location.href='order_details.php?OID=$OID&page=shopOrder_preview';">order details</button></td>
                        EOT;
                        
                        if($status=='Not Finished'){
                        echo <<< EOT
                            <td>
                            <button type="button" class="btn btn-success" onclick="javascript:location.href='shopOrder_done.php?OID=$OID';">Done</button>
                            <button type="button" class="btn btn-danger" onclick="javascript:location.href='shopOrder_cancel.php?OID=$OID';">Cancel</button>
                            </td>
                            </tr>
                        EOT;
                        }
                        else{
                        echo <<< EOT
                            <td> </td>
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
  </div>

  <!-- Option 1: Bootstrap Bundle with Popper -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script> -->
  <!-- Option 2: Separate Popper and Bootstrap JS -->
  <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
</body>

</html>