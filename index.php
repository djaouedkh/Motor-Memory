<?php
    // authorized cookie and created session
        session_start();


    // connect to mysql
        try{
            $bdd = new PDO("mysql:host=localhost;dbname=motormemory", "root", "");
        }catch(PDOException $e) {
            echo "message:" . $e->getMessage();
        }
require("todolist.php");







?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motor Memory</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="main">

        <!---------------------input for add todolist------------------------------>
            <form action="" method="post" class='add-todolist'>
                <input type="text" name="name-todolist">
                <button type="submit" name="button-add">Save your memories</button>
            </form>
        <!-- ------------------------------------------------------------------------ -->
        <!-- ---------------------input for modify todolist------------------- -->
            <?php 
                if (isset($todolist)) {
                    // create form modify and display in the input the basic values
                    echo "
                    <form class='modify' action='' method='post'>
                        <input type='text' name='name-modify' placeholder='Tâches' value='" . $todolist[0]['nameTodolist'] . "'>
                        <input type='text' name='state-modify' placeholder='Fait/Pas fait' value='" . $todolist[0]['state'] . "'>
                        <button type='submit' name='registered-modify' value='" . $todolist[0]['idTodolist'] . "'>Enregistrer vos modifications</button>
                    </form>";
                }
            ?>
        <!-- -------------------------------------------------------------------- -->

        <!-- -----------------------------display todolist--------------------------------- -->
            <div class="container-idea">
                <div class="list">
                    <div>
                        <?php
                            // displays our tasks
                            if (isset($_SESSION["idUser"])) {
                                
                                // selects everything in the table todolist where iduser is located
                                $selectNameTodolistState = $bdd->prepare("SELECT * FROM todolist WHERE idUser = ?");
                                $selectNameTodolistState->execute([$_SESSION["idUser"]]);

                                // fetches these data
                                $bddTodolist = $selectNameTodolistState->fetchAll();

                                // displays todolist
                                // add a value to the buttons to assign them the idTodolist and differentiate them when clicked
                                foreach ($bddTodolist as $todolistDisplay) {
                                    echo  "<div class='todolist'>" . $todolistDisplay["nameTodolist"] . " " . $todolistDisplay["state"] . "</div>
                                        <form action='' method='post'>
                                            <button type='submit' name='button-modify' value=".$todolistDisplay["idTodolist"]." class='button-modify'>Modify name</button>
                                            <button type='submit' name='button-delete' value=".$todolistDisplay["idTodolist"]." class='button-delete'>Delete</button>
                                        </form>" . "<br>"; 
                                }  
                            }      
                        ?>
                    </div>
                </div>
            </div>
        <!-- -------------------------------------------------------------------------------------- -->

        <!-- ---------------------DISCONNECT BUTTON------------------------ -->
            <?php
                if (isset($_SESSION['idUser'])) {
                    echo " <a href='disconnect.php'>Déconnexion</a>
                        ";
                }
            ?>
        <!-- ---------------------------------------------------------------- -->
    </div>

    <!-- ----------------CONTAINER FORM-------------------- -->
        <?php
            if (isset($_SESSION['idUser'])) {
            
            }
            else{ 
                echo"<a href='connect.php'>Connexion</a>
                <a href='registered.php'>Inscription</a>";
            }
            
        ?>
        <!--------------------display connect or registration or error-------------------->

            <div>
                <?php
                    if (isset($error)) {
                        echo $error;
                    }
                    if (isset($connect)) {
                        echo $connect . $loginConnect;
                    }
                    if (isset($registered)) {
                        echo $registered . $loginRegistration;
                    }
                ?>
            </div>
        <!-- ---------------------------------------------------------------------- -->

    <!-- ------------------------------------------------------- -->




</body>
</html>








