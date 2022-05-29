<?php
    session_start();

    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';



    // if(!isset($_POST['shop_name'])||!isset($_POST['distance']) || !isset($_POST['lower_price'])|| !isset($_POST['higher_price'])||!isset($_POST['meal_name'])||!isset($_POST['shop_category'])){
    //     echo 'FAILED';
    //     exit();
    // }
    // echo 'distance = '.$_POST['filter_distance']."<br>";
    // echo 'shop_name = '.$_POST['filter_sname']."<br>";
    // echo 'lower_price = '.$_POST['filter_lwprice']."<br>";
    // echo 'higher_price = '.$_POST['filter_hiprice']."<br>";
    // echo 'meal_name = '.$_POST['filter_mname']."<br>";
    // echo 'shop_category = '.$_POST['filter_scat']."<br>";
    $ulon = $_SESSION['ulon'];
    $ulat = $_SESSION['ulat'];
    $fdis =  $_POST['filter_distance'];
    $fsname = $_POST['filter_sname'];//ok
    $flp = $_POST['filter_lwprice'];
    $fhp = $_POST['filter_hiprice'];
    $fmname = $_POST['filter_mname'];
    $fscat = $_POST['filter_scat'];//ok
    $_SESSION['doSearch'] = $_POST['doSearch'];

    $a = 3000;
    $b = 10000;
    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $conditions = [];
    $parameters = [];
    
    // conditional statements
    if (!empty($fsname)){
        // here we are using LIKE with wildcard search
        // use it ONLY if really need it
        $conditions[] = 'shop_name LIKE ?';
        $parameters[] = '%'.$fsname."%";
    }
    
    if (!empty($fscat)){
        $conditions[] = 'shop_category LIKE ?';
        $parameters[] = '%'.$fscat."%";
    }

    if (!empty($fmname)){
        $conditions[] = 'meal_name LIKE ?';
        $parameters[] = '%'.$fmname."%";
    }
    
    if(!empty($flp) && !empty($fhp)){
        if($flp > $fhp){
            $tmp = $flp;
            $flp = $fhp;
            $fhp = $tmp;
        }
        $conditions[] = 'price between ? and ?';
        $parameters[] = $flp;
        $parameters[] = $fhp;
    }
    else{
        if(!empty($flp)){
            $conditions[] = 'price >= ?';
            $parameters[] = $flp;
        }
        else if(!empty($fhp)){
            $conditions[] = 'price <= ?';
            $parameters[] = $fhp;
        }
    }

    if($fdis == 'near'){
        $conditions[] = "ST_Distance_Sphere(POINT($ulon,$ulat), location) < ?";
        $parameters[] = $a;
    }
    else if($fdis == 'medium'){
        $conditions[] = "ST_Distance_Sphere(POINT($ulon,$ulat), location) between ? and ?";
        $parameters[] = $a;
        $parameters[] = $b;
    }
    else if($fdis == 'far'){
        $conditions[] = "ST_Distance_Sphere(POINT($ulon,$ulat), location) > ?";
        $parameters[] = $b;
    }
    
    // the main query 
    // use distinct shop_name to get shop_name only once
    // ST_Distance_Sphere Returns linear distance in meters between two lon/lat points
    $sql = "select distinct shop_name, shop_category, ST_Distance_Sphere(POINT($ulon,$ulat), location) as distance from shops natural join menus";
    
    // 把條件組合成 query 語法
    if ($conditions)
    {
        $sql .= " where ".implode(" AND ", $conditions);
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($parameters);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //距離判斷
    foreach($result as &$srow){
        if($srow['distance'] < $a || $srow['distance'] == $a){
            $srow['distance'] = 'near';
        }
        else if($srow['distance'] < $b || $srow['distance'] == $b){
            $srow['distance'] = 'medium';
        }
        else{
            $srow['distance'] = 'far';
        }
    }
    $_SESSION['filted_result'] = $result;
    // print_r($result);


    header("Location: nav.php");
    exit();
?>