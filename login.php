<?php
    session_start();
    $_SESSION['Authenticated']=false;

    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';


    try{
        if(!isset($_POST['uacc'])||!isset($_POST['pwd'])){
            header("Location: index.php");
            exit();
        }
        if(empty($_POST['uacc'])||empty($_POST['pwd'])){
            throw new Exception('欄位空白');
        }
        $uacc = $_POST['uacc'];
        $pwd = $_POST['pwd'];
        $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("select * from users where account=:account");
        $stmt->execute(array('account'=>$uacc));

        if($stmt->rowCount()==1){
            $row = $stmt->fetch();
            if($row['password'] == hash('sha256', $row['salt'].$_POST['pwd'])){
                $_SESSION['Authenticated'] = true;
                $_SESSION['Name'] = $row['name'];
                $_SESSION['Phonenumber'] = $row['phonenumber'];
                $_SESSION['Account'] = $row['account'];
                $_SESSION['Latitude'] = $row['latitude'];
                $_SESSION['Longitude'] = $row['longitude'];
                header("Location: nav.php");
                exit();
            }
            else{
                throw new Exception('Login failed.');
            }
        }
        else{
            throw new Exception("Login failed.");
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
                    window.location.replace("index.php");
                    </script>
                </body>
            </html>
        EOT;
    }
?>