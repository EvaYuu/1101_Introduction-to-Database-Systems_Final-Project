<?php
    session_start();
    $_SESSION['Authenticated']=false;

    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';


    try{
        $message ='';
        $isException = false;
        if(!isset($_POST['uname']) || !isset($_POST['uphone']) || !isset($_POST['uacc']) || !isset($_POST['pwd'])
            || !isset($_POST['re_pwd']) || !isset($_POST['ulat']) || !isset($_POST['ulon'])){
            header("Location: index.php");
            exit();
        }
        //欄位空白
        if(empty($_POST['uname'])){
            $isException = true;
            $message = $message.'name欄位空白 ';
        }
        if(empty($_POST['uphone'])){
            $isException = true;
            $message = $message.'phone number欄位空白 ';
        }
        if(empty($_POST['uacc'])){
            $isException = true;
            $message = $message.'account欄位空白 ';
        }
        if(empty($_POST['pwd'])){
            $isException = true;
            $message = $message.'password欄位空白 ';
        }
        if(empty($_POST['re_pwd'])){
            $isException = true;
            $message = $message.'re-type password欄位空白 ';
        }
        if(empty($_POST['ulat'])){
            $isException = true;
            $message = $message.'latitude欄位空白 ';
        }
        if(empty($_POST['ulon'])){
            $isException = true;
            $message = $message.'longitude欄位空白 ';
        }

        // if($_POST['pwd']!=$_POST['re_pwd']){
        //     $isException = true;
        //     throw new Exception('密碼驗證 ≠ 密碼');
        // }
        // if(!ctype_alnum($_POST['pwd']) || !ctype_alnum($_POST['uacc']) || !(strlen($_POST['uphone'])==10 && is_numeric($_POST['uphone']))
        //     || !is_numeric($_POST['ulat']) || !is_numeric($_POST['ulon'])
        //     || strval($_POST['ulat'])>90.0 || strval($_POST['ulat'])<-90.0 || strval($_POST['ulon'])>180.0 || strval($_POST['ulon'])<-180.0){
        //     throw new Exception('輸入格式不對');
        // }
        if($isException == true){
            throw new Exception($message);
        }
        $uname = $_POST['uname'];
        $uphone = $_POST['uphone'];
        $uacc = $_POST['uacc'];
        $pwd = $_POST['pwd'];
        $ulat = $_POST['ulat'];
        $ulon = $_POST['ulon'];
        $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("select account from users where account=:account");
        $stmt->execute(array('account'=>$uacc));

        if($stmt->rowCount()==0){
            $salt = strval(rand(1000,9999));
            $hashvalue = hash('sha256', $salt.$pwd);
            $stmt = $conn->prepare("insert into users(name, phonenumber, account, password, salt, latitude, longitude)
            values(:name,:phonenumber, :account, :password, :salt, :latitude, :longitude)");
            $stmt->execute(array('name'=>$uname, 'phonenumber'=>$uphone, 'account'=>$uacc, 'password'=>$hashvalue, 'salt'=>$salt, 'latitude'=>$ulat, 'longitude'=>$ulon));
            $_SESSION['Authenticated'] = true;
            $_SESSION['Name'] = $uname;
            $_SESSION['Phonenumber'] = $uphone;
            $_SESSION['Account'] = $uacc; 
            $_SESSION['Lattitude'] = $ulat; 
            $_SESSION['Longitude'] = $ulon;
            echo <<< EOT
            <!DOCTYPE>
            <html>
                <body>
                    <script>
                    alert("Register success!!");
                    window.location.replace("index.php");
                    </script>
                </body>
            </html>
            EOT;
            exit();
        }
        else{
            throw new Exception("Account has been registered!!");
        }
    }
    catch(Exception $e){
        $msg = $e->getMessage();
        session_unset();
        session_destroy();
        echo <<< EOT
            <!DOCTYPE>
            <html>
                <body>
                    <script>
                    alert("$msg");
                    window.location.replace("signUp.php");
                    </script>
                </body>
            </html>
        EOT;
    }
?>