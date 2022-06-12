<?php
    session_start();

    $dbservername='localhost';
    $dbname='databasehw';
    $dbusername='root';
    $dbpassword='';

    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname",$dbusername,$dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try{
        if(!isset($_GET['OID'])){
            throw new Exception('Something error');
        }
        if(!isset($_SESSION['Authenticated'])||$_SESSION['Authenticated']!=true){
            header("Location: index.php");
            exit();
        }
        $OID = $_GET['OID'];

        $conn->beginTransaction();
        //update order status (Not Finished->Finished)
        $stmt = $conn->prepare("update orders set status='Finished' where OID=:OID");
        $stmt->execute(array('OID'=>$OID));
        $conn->commit();

        echo <<< EOT
            <!DOCTYPE>
            <html>
                <body>
                    <script>
                    alert('Done SUCCESS');
                    window.location.replace("shopOrder.php");
                    </script>
                </body>
            </html>
        EOT;
        exit();
    }
    catch(Exception $e){
        if($conn->inTransaction()){
            $conn->rollBack();
            $msg='Cancel FAIL';
        }
        $msg = $e->getMessage();
        echo <<< EOT
            <!DOCTYPE>
            <html>
                <body>
                    <script>
                    alert("$msg");
                    window.location.replace("shopOrder.php");
                    </script>
                </body>
            </html>
        EOT;

    }
?>