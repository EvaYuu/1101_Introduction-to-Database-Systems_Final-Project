<?php include("shop.php");?>
<?php

    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    try{
        if(!isset($_POST['edit_price']) || !isset($_POST['edit_quantity'])){
            echo 'wrong here<br>';
            header("Location: shop.php");
            exit();
        }
        if(empty($_POST['edit_price']) || empty($_POST['edit_quantity'])){
            throw new Exception('欄位空白');
        }
        if(!(floor($_POST['edit_price'])==$_POST['edit_price']) || !(floor($_POST['edit_quantity'])==$_POST['edit_quantity']) 
            || ($_POST['edit_price'])<0 || ($_POST['mquan'])<0){
            throw new Exception('輸入格式不對');
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
                    window.location.replace("menu.php");
                    </script>
                </body>
            </html>
        EOT;
    }
?>