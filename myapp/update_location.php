<?php
    session_start();

    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';
    
    $uacc = $_SESSION['Account'];
    $new_ulat = $_POST['new_ulat'];
    $new_ulon = $_POST['new_ulon'];

    try{
        $message ='';
        $isException = false;
        if(!isset($new_ulat) || !isset($new_ulon)){
            header("Location: nav.php");
            exit();
        }
        //欄位空白
        if(empty($new_ulat) && $new_ulat != 0){
            $isException = true;
            $message = $message.'latitude 欄位空白\n';
        }
        if(empty($new_ulon) && $new_ulon != 0){
            $isException = true;
            $message = $message.'longitude 欄位空白\n';
        }
        //輸入格式不對
        if(!empty($_POST['ulat']) && (!is_numeric($_POST['ulat']) || strval($_POST['ulat'])>90.0 || strval($_POST['ulat'])<-90.0)){
            $isException = true;
            $message = $message.'latitude 輸入格式不對\n';
        }
        if(!empty($_POST['ulon']) && (!is_numeric($_POST['ulon']) || strval($_POST['ulon'])>180.0 || strval($_POST['ulon'])<-180.0)){
            $isException = true;
            $message = $message.'longitude 輸入格式不對\n';
        }
        //final
        if($isException == true){
            throw new Exception($message);
        }
        $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("update users set location=ST_GeomFromText(:location) where account='$uacc'");
        $stmt->execute(array('location'=>'POINT(' . $new_ulon . ' ' . $new_ulat . '))'));
        echo <<< EOT
        <!DOCTYPE>
        <html>
            <body>
                <script>
                alert("Location update success!!");
                window.location.replace("nav.php");
                </script>
            </body>
        </html>
        EOT;
        exit();
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