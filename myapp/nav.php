<?php
  session_start();

  $dbservername='localhost';
  $dbname='databasehw';
  $dbusername='root';
  $dbpassword='';

  $uacc = $_SESSION['Account']; 
  $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $stmt = $conn->prepare("select name,phonenumber,ST_AsText(location) as location,role,walletbalance from users where account=:account");
  $stmt->execute(array('account'=>$uacc));
  $row = $stmt->fetch();
  $uname = $row['name'];
  $uphone = $row['phonenumber'];
  $uloc = $row['location'];
  $urole = $row['role'];
  $uphone = str_pad($uphone,10,"0",STR_PAD_LEFT);
  $uwal = $row['walletbalance'];
  $ulat = '';
  $ulon = '';
  $change = false;
  # split to $ulon & $ulat
  foreach(str_split($uloc) as $s){
    if(is_numeric($s) || $s == '.' || $s == '-'){
      if($change){
        $ulat = $ulat.$s;
      }
      else{
        $ulon = $ulon.$s;
      }
    }
    else if($s == ' '){
      $change = true; 
    }
  }
  $_SESSION['ulat'] = $ulat;
  $_SESSION['ulon'] = $ulon;
  $_SESSION['uwal'] = $uwal;

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
      <li class="active"><a href="nav.php">Home</a></li>
      <li><a href="shop.php">Shop</a></li>
      <li><a href="myOrder.php">MyOrder</a></li>
      <li><a href="shopOrder.php">Shop Order</a></li>
      <li><a href="transactionRecord.php">Transaction Record</a></li>
      <li><a href="Logout.php">Log out</a></li>
    </ul>

    <div class="tab-content">
      <div id="home" class="tab-pane fade in active">
        <h3>Profile</h3>
        <div class="row">
          <div class="col-xs-12">
            Account: <?php echo $uname; ?>, <?php echo $urole; ?>, PhoneNumber: <?php echo $uphone; ?>,  location: <?php echo $ulat; ?>, <?php echo $ulon; ?>
            
            <button type="button " style="margin-left: 5px;" class=" btn btn-info " data-toggle="modal"
            data-target="#location">edit location</button>
            <!--  -->
            <div class="modal fade" id="location"  data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
              <div class="modal-dialog  modal-sm">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">edit location</h4>
                  </div>

                  <form action="update_location.php" method="post">
                  <div class="modal-body">
                    <label class="control-label " for="latitude">latitude</label>
                    <input type="text" name="new_ulat" class="form-control" id="latitude" placeholder="enter latitude"><br>
                    <label class="control-label " for="longitude">longitude</label>
                    <input type="text" name="new_ulon" class="form-control" id="longitude" placeholder="enter longitude">
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-default">Edit</button>
                  </div>
                  </form>

                </div>
              </div>
            </div>
           <!--  -->
            walletbalance: <?php echo $uwal; ?>
            <!-- Modal -->
            <button type="button " style="margin-left: 5px;" class=" btn btn-info " data-toggle="modal"
              data-target="#myModal">Add value</button>
            <div class="modal fade" id="myModal"  data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
              <div class="modal-dialog  modal-sm">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add value</h4>
                  </div>

                  <form action="update_wallet.php" method="post">
                  <div class="modal-body">
                    <input type="text" name="add_value" class="form-control" id="value" placeholder="enter add value">
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-default">Add</button>
                  </div>
                  </form>

                </div>
              </div>
            </div>
          </div>
        </div>

        <!--  Search   -->
        <h3>Search</h3>
        <div class=" row  col-xs-8">
          <form class="form-horizontal" action="shop_filter.php" method='post'>
            <div class="form-group">
              <label class="control-label col-sm-1" for="Shop">Shop </label>
              <div class="col-sm-5">
                <input type="text" class="form-control" placeholder="Enter Shop name" name="filter_sname">
              </div>
              <label class="control-label col-sm-1" for="distance">distance</label>
              <div class="col-sm-5">
                <select class="form-control" id="sel1" name="filter_distance">
                  <option>no select</option>
                  <option>near</option>
                  <option>medium </option>
                  <option>far</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-1" for="Price">Price</label>
              <div class="col-sm-2">
                <input type="text" class="form-control" name="filter_lwprice">
              </div>
              <label class="control-label col-sm-1" for="~">~</label>
              <div class="col-sm-2">
                <input type="text" class="form-control" name="filter_hiprice">
              </div>
              <label class="control-label col-sm-1" for="Meal">Meal</label>
              <div class="col-sm-5">
                <input type="text" list="Meals" class="form-control" id="Meal" placeholder="Enter Meal" name="filter_mname">
                <datalist id="Meals">
                  <option value="Hamburger">
                  <option value="coffee">
                </datalist>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-1" for="category"> category</label>              
                <div class="col-sm-5">
                  <input type="text" list="categorys" class="form-control" id="category" placeholder="Enter shop category" name="filter_scat">
                  <datalist id="categorys">
                    <option value="fast food">               
                  </datalist>
                </div>
                <button type="submit" style="margin-left: 18px;"class="btn btn-primary">Search</button>  
            </div>
            <input type="hidden" name="doSearch" value="1">
          </form>
        </div>
        <div class="row">
          <div class="  col-xs-8">
            <table class="table" style=" margin-top: 15px;">
              <thead>
                <tr>
                  <th scope="col">#</th>                
                  <th scope="col">shop name</th>
                  <th scope="col">shop category</th>
                  <th scope="col">Distance</th>               
                </tr>
              </thead>
              <tbody>
                <?php
                  if(isset($_SESSION['filted_result'])){
                    // print_r($_SESSION['filted_result']);
                    $index = 0;
                    $shop_rows = $_SESSION['filted_result'];
                    foreach($shop_rows as $rows){
                      $index += 1;
                      $shop_name = htmlentities($rows['shop_name']);
                      $shop_category = htmlentities($rows['shop_category']);
                      $shop_distance = htmlentities($rows['distance']);
                      echo <<< EOT
                      <tr>
                        <th scope="row">$index</th>               
                        <td>$shop_name</td>
                        <td>$shop_category</td>                
                        <td>$shop_distance </td>
                        <td><button type="button" class="btn btn-info " onclick="javascript:location.href='open_menu.php?shop_name=$shop_name';">Open menu</button></td>            
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
  <script>
    $(document).ready(function () {
      $(".nav-tabs a").click(function () {
        $(this).tab('show');
      });
    });
  </script>

  <!-- Option 2: Separate Popper and Bootstrap JS -->
  <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
</body>

</html>
