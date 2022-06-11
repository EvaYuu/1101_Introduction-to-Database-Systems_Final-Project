<?php include("shop.php");?>
<?php
    //session_start();

    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    try{
        $message ='';
        $isException = false;
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
        // 欄位空白
        if(empty($_POST['mname'])){
            $isException = true;
            $message = $message.'meal name 欄位空白\n';
        }
        if(empty($_POST['mprice']) && $_POST['mprice'] != 0){
            $isException = true;
            $message = $message.'price 欄位空白\n';
        }
        if(empty($_POST['mquan']) && $_POST['mquan'] != 0){
            $isException = true;
            $message = $message.'quantity 欄位空白\n';
        }
        // 輸入格式
        if(!empty($_POST['mprice']) && (!is_numeric($_POST['mprice']) || strval($_POST['mprice']) < 0)){
            $isException = true;
            $message = $message.'price 輸入格式錯誤\n';
        }
        if(!empty($_POST['mprice']) && (!is_numeric($_POST['mquan']) || strval($_POST['mquan']) < 0)){
            $isException = true;
            $message = $message.'quantity 輸入格式錯誤\n';
        }
        $sname = $_SESSION['Shop_name'];
        $mname = $_POST['mname'];
        $mprice = $_POST['mprice'];
        $mquan = $_POST['mquan'];

        if($_FILES['mimg']['error']!=0){
            $isException = true;
            $message = $message.'image 上傳錯誤\n';
        }

        //final
        if($isException == true){
            throw new Exception($message);
        }
        
        $file = fopen($_FILES["mimg"]["tmp_name"], "rb");
        $fileContents = fread($file, filesize($_FILES["mimg"]["tmp_name"]));
        fclose($file);
        $fileContents = base64_encode($fileContents);
        $image_type = $_FILES["mimg"]["type"];  
        
        $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("select meal_name from menus where shop_name=:shop_name and meal_name=:meal_name");
        $stmt->execute(array('shop_name'=>$sname, 'meal_name'=>$mname));
        
        if($stmt->rowCount()==0){
            
            $stmt = $conn->prepare("insert into menus(shop_name, meal_name, price, quantity, image, image_type)
            values(:shop_name,:meal_name, :price, :quantity, :image, :image_type)");
            $stmt->execute(array('shop_name'=>$sname, 'meal_name'=>$mname, 'price'=>$mprice, 'quantity'=>$mquan, 'image'=>$fileContents, 'image_type'=>$image_type));
            
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
                    window.location.replace("shop.php");
                    </script>
                </body>
            </html>
        EOT;
    }
?>