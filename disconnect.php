<?php 

    // authorized cookie and created session
    session_start();


    // connect to mysql
        try{
            $bdd = new PDO("mysql:host=localhost;dbname=motormemory", "root", "");
        }catch(PDOException $e) {
            echo "message:" . $e->getMessage();
        }

                    // empties the cookies and destroy session
                    $_SESSION = [];
                    session_destroy();
                    header("Location: index.php");

?>