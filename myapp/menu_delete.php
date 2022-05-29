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
        echo "$mname";
        if($mname==''){
            throw new Exception('Something error');
        }
        $mname = $_GET['mname'];
        $stmt = $conn->prepare("delete from menus where shop_name=:sname and meal_name=:mname");
        $stmt->execute(array('sname'=>$sname, 'mname'=>$mname));
        //throw new Exception('DELETE SUCCESS');
        //exit();
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