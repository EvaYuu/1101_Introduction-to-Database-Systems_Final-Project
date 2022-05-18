<?php include("shop.php");?>
<?php

    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    $sname = $_SESSION['Shop_name'];
    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    try{
        if(!isset($_POST['edit_price']) || !isset($_POST['edit_quantity'])){
            echo 'wrong here<br>';
            header("Location: shop.php");
            exit();
        }
        $flag = 1;
        foreach($_POST['edit_price'] as $k => $v){
            if($v != ''){
                $flag = 0;
                if( floor($v)!=$v || $v<0){
                    throw new Exception('輸入格式不對');
                }
                else{
                    $stmt = $conn->prepare("update menus set price=:price where shop_name='$sname' and meal_name='$k'");
                    $stmt->execute(array('price'=>$v));
                    throw new Exception('修改成功');
                }
            }
        }
        foreach($_POST['edit_quantity'] as $k => $v){
            if($v != ''){
                $flag = 0;
                if( floor($v)!=$v || $v<0){
                    throw new Exception('輸入格式不對');
                }
                else{
                    $stmt = $conn->prepare("update menus set quantity=:quantity where shop_name='$sname' and meal_name='$k'");
                    $stmt->execute(array('quantity'=>$v));
                    throw new Exception('修改成功');
                }
            }
        }
        if($flag){
            throw new Exception('欄位空白');
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