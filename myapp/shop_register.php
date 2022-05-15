<?php
    session_start();

    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    $uacc = $_SESSION['Account'];

    try{
        $message ='';
        $isException = false;
        if(!isset($_POST['sname']) || !isset($_POST['scat']) || !isset($_POST['slat']) || !isset($_POST['slon'])){
            header("Location: shop.php");
            exit();
        }
        //欄位空白
        if(empty($_POST['sname'])){
            $isException = true;
            $message = $message.'shop name 欄位空白\n';
        }
        if(empty($_POST['scat'])){
            $isException = true;
            $message = $message.'shop category 欄位空白\n';
        }
        if(empty($_POST['slat'])){
            $isException = true;
            $message = $message.'latitude 欄位空白\n';
        }
        if(empty($_POST['slon'])){
            $isException = true;
            $message = $message.'longitude 欄位空白\n';
        }
        //輸入格式不對
        if(!empty($_POST['slat']) && (!is_numeric($_POST['slat']) || ($_POST['slat'])>90.0 || ($_POST['slat'])<-90.0)){
            $isException = true;
            $message = $message.'latitude 輸入格式不對\n';
        }
        if(!empty($_POST['slon']) && (!is_numeric($_POST['slon']) || ($_POST['slon'])>180.0 || ($_POST['slon'])<-180.0)){
            $isException = true;
            $message = $message.'longitude 輸入格式不對\n';
        }
        //final
        if($isException == true){
            throw new Exception($message);
        }
        $sname = $_POST['sname'];
        $scat = $_POST['scat'];
        $slat = $_POST['slat'];
        $slon = $_POST['slon'];
        $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("select shop_name from shops where owner=:owner");
        $stmt->execute(array('owner'=>$uacc));

        if($stmt->rowCount()==0){
            $stmt = $conn->prepare("insert into shops(owner, shop_name, shop_category, latitude, longitude)
            values(:owner,:shop_name, :shop_category, :latitude, :longitude)");
            $stmt->execute(array('owner'=>$uacc, 'shop_name'=>$sname, 'shop_category'=>$scat, 'latitude'=>$slat, 'longitude'=>$slon));
            
            $stmt = $conn->prepare("update users set role='manager' where account='$uacc'");
            $stmt->execute();

            $_SESSION['Shop_name'] = $sname;
            $_SESSION['Shop_category'] = $scat;
            $_SESSION['Shop_latitude'] = $slat;
            $_SESSION['Shop_longitude'] = $slon;

            echo <<< EOT
            <!DOCTYPE>
            <html>
                <body>
                    <script>
                    alert("Register success!!");
                    window.location.replace("shop.php");
                    </script>
                </body>
            </html>
            EOT;
            exit();
        }
        else{
            throw new Exception("shop name has been registered!!");
        }
    }
    catch(Exception $e){
        $msg = $e->getMessage();
        echo <<< EOT
            <!DOCTYPE>
            <html>
                <body>
                    <script>
                    alert("$msg");
                    window.location.replace("shop.php");
                    </script>
                </body>
            </html>
        EOT;
    }
?>