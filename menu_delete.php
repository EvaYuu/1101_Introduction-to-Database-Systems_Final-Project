<?php
    session_start();
    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    $sname = $_SESSION['Shop_name'];
    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $mname = $_GET['mname'];

    try{
        if($mname==''){
            throw new Exception('Something error');
        }
        $mname = $_GET['mname'];
        $stmt = $conn->prepare("delete from menus where shop_name='$sname' and meal_name='$mname'");
        $stmt->execute();
        throw new Exception('DELETE SUCCESS');
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