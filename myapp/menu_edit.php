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
        $message = '';
        $nullsubmit = 1;
        if(!isset($_POST['edit_price']) || !isset($_POST['edit_quantity'])){
            echo 'wrong here<br>';
            header("Location: shop.php");
            exit();
        }
        foreach($_POST['edit_price'] as $k => $v){
            if($v != ''){
                $nullsubmit = 0;
                if( !is_numeric($v) || floor($v)!=$v || strval($v) < 0){
                    $message = $message.'edit price 輸入格式錯誤\n';
                }
                else{
                    $stmt = $conn->prepare("update menus set price=:price where shop_name='$sname' and meal_name='$k'");
                    $stmt->execute(array('price'=>$v));
                    $message = $message.'price 修改成功\n';
                }
            }
        }
        foreach($_POST['edit_quantity'] as $k => $v){
            if($v != ''){
                $nullsubmit = 0;
                if( !is_numeric($v) || floor($v)!=$v || strval($v) < 0){
                    $message = $message.'edit quantity 輸入格式錯誤\n';
                }
                else{
                    $stmt = $conn->prepare("update menus set quantity=:quantity where shop_name='$sname' and meal_name='$k'");
                    $stmt->execute(array('quantity'=>$v));
                    $message = $message."quantity 修改成功\n";
                }
            }
        }
        if($nullsubmit){
            throw new Exception('欄位空白');
        }
        else{
            
            echo <<< EOT
            <!DOCTYPE>
            <html>
                <body>
                    <script>
                    alert("$message");
                    window.location.replace("shop.php");
                    </script>
                </body>
            </html>
            EOT;
            exit();
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
        exit();
    }
?>