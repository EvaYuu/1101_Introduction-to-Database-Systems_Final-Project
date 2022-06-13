<?php
    session_start();
    header("Refresh: 20;url='transactionRecord_preview.php'");
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
      <li><a href="shopOrder_preview.php">Shop Order</a></li>
      <li class="active"><a href="transactionRecord_preview.php">Transaction Record</a></li>
      <li><a href="Logout.php">Log out</a></li>
    </ul>

    <div class="tab-content">
        <div class=" row  col-xs-8">
          
          <form class="form-horizontal" action="transaction_filter.php" method='post'>
            <div class="form-group"><br>
              <label class="control-label col-sm-1" for="action">Action</label>
              <div class="col-sm-5">
                <select class="form-control" id="filter" name="filter_action" onchange="this.form.submit()">
                  <option>-- Select --</option>
                  <option selected="selected">All</option>
                  <option>Payment</option>
                  <option>Receive</option>
                  <option>Recharge</option>
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
                  <th scope="col">Record ID</th>
                  <th scope="col">Action</th>
                  <th width="80%" scope="col">Time</th>
                  <th scope="col">Trader</th>
                  <th scope="col">Amount change</th>
                </tr>
              </thead>
              <tbody>                
                  <?php
                    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $stmt = $conn->prepare("select * from transactions where account=:account");
                    $stmt->execute(array('account'=>$uacc));
                    $mrow = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($mrow as $row) {
                        $record_id = htmlentities($row['record_id']);
                        $action = htmlentities($row['action']);
                        $time = htmlentities($row['time']);
                        $trader = htmlentities($row['trader']);
                        $amount_change = htmlentities($row['amount_change']);
                        //force to show the sign
                        $amount_change = sprintf('%+d', $amount_change);
                        
                        echo <<< EOT
                            <tr>
                            <th scope="row">$record_id</th>
                            <td>$action</td>
                            <td>$time</td>
                            <td>$trader</td>
                            <td>$amount_change</td>
                            </tr>
                        EOT;
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