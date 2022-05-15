<?php include("shop.php");?>
<?php
    //session_start();

    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    try{
        if(empty($_SESSION['Shop_name'])){
            echo <<< EOT
            <!DOCTYPE>
            <html>
                <body>
                    <script>
                    alert("尚未註冊店家，無法新增餐點。");
                    window.location.replace("shop.php");
                    </script>
                </body>
            </html>
            EOT;
        }
        if(!isset($_POST['mname']) || !isset($_POST['mprice']) || !isset($_POST['mquan'])){
            echo 'wrong here<br>';
            header("Location: shop.php");
            exit();
        }
        if(empty($_POST['mname']) || empty($_POST['mprice']) || empty($_POST['mquan'])){
            throw new Exception('欄位空白');
        }
        if(!(floor($_POST['mprice'])==$_POST['mprice']) || !(floor($_POST['mquan'])==$_POST['mquan']) 
            || ($_POST['mprice'])<0 || ($_POST['mquan'])<0){
            throw new Exception('輸入格式不對');
        }
        $sname = $_SESSION['Shop_name'];
        $mname = $_POST['mname'];
        $mprice = $_POST['mprice'];
        $mquan = $_POST['mquan'];
        //$mimg = $_POST['mimg']; 

        if($_FILES['mimg']['error']!=0){
            throw new Exception('上傳錯誤');
        }
        
        $file = fopen($_FILES["mimg"]["tmp_name"], "rb");
        $fileContents = fread($file, filesize($_FILES["mimg"]["tmp_name"]));
        fclose($file);
        $fileContents = base64_encode($fileContents);
        
        
        $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("select meal_name from menus where shop_name=:shop_name and meal_name=:meal_name");
        $stmt->execute(array('shop_name'=>$sname, 'meal_name'=>$mname));
        
        if($stmt->rowCount()==0){
            
            $stmt = $conn->prepare("insert into menus(shop_name, meal_name, price, quantity, image)
            values(:shop_name,:meal_name, :price, :quantity, :image)");
            $stmt->execute(array('shop_name'=>$sname, 'meal_name'=>$mname, 'price'=>$mprice, 'quantity'=>$mquan, 'image'=>$fileContents));
            
            echo <<< EOT
            <!DOCTYPE>
            <html>
                <body>
                    <script>
                    alert("Adding success!!");
                    window.location.replace("shop.php");
                    </script>
                </body>
            </html>
            EOT;
            exit();
        }
        else{
            throw new Exception("Meal has been already added into menu!!");
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