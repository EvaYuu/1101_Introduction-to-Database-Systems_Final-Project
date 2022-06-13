<?php
    session_start();
    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    $sname = $_SESSION['Shop_name'];
    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try{
        if(!isset($_GET['mrow'])){
            throw new Exception('Something error');
        }
        $mri = $_GET['mrow'];
        $mri -= 1;
        

        $stmt = $conn->prepare("select shop_name, meal_name from menus where shop_name='$sname'");
        $stmt->execute();
        $mrow = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $mname = $mrow[$mri]['meal_name'];
        //check meal not in unfinished
        $stmt = $conn->prepare("select * from order_menus natural join orders where shop_name = :shop_name and status='Not Finished' and meal_name = :meal_name;");
        $stmt->execute(array('shop_name'=>$sname, 'meal_name'=>$mname));

        if($stmt->rowCount()!=0){
            throw new Exception('There is unfinished order so you cannot delete it!');
        }

        $stmt = $conn->prepare("delete from menus where shop_name='$sname' and meal_name='$mname'");
        $stmt->execute();
        throw new Exception('DELETE SUCCESS');
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
                    window.location.replace("shop.php");
                    </script>
                </body>
            </html>
        EOT;

    }
?>