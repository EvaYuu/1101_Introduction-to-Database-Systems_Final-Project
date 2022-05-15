<?php
    session_start();
    session_destroy();
    
    $msg = 'logout success.';
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
?>