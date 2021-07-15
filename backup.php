<?php
    // authorized cookie and created session
        session_start();


    // connect to mysql
        try{
            $bdd = new PDO("mysql:host=localhost;dbname=motormemory", "root", "");
        }catch(PDOException $e) {
            echo "message:" . $e->getMessage();
        }

    // ----------------- REGISTRATION --------------------------

        if (isset($_POST["button-registration"])) {
            // retrieves input login , email , password and confirm password values
            $loginRegistration = $_POST["login-registration"];
            $emailRegistration = $_POST["email-registration"];
            $passwordRegistration = $_POST["password-registration"];
            $confirmPasswordRegistration = $_POST["confirm-password-registration"];

            // check that user fills all input
            if (!empty($loginRegistration) && !empty($emailRegistration) && !empty($passwordRegistration) && !empty($confirmPasswordRegistration)) {
                
                // check that email is available
                if (filter_var($emailRegistration, FILTER_VALIDATE_EMAIL)) {
                    
                    // check that the password is identical
                    if ($passwordRegistration === $confirmPasswordRegistration) {

                        // connect to mysql
                        // try{
                        //     $bdd = new PDO("mysql:host=localhost;dbname=motormemory", "root", "");
                        // }catch(PDOException $e) {
                        //     echo "message:" . $e->getMessage();
                        // }
                        
                        // request to mysql to check if the login and email exists
                        $selectLogin = $bdd->prepare("SELECT * FROM users WHERE login = ?");
                        $selectLogin->execute([$loginRegistration]);
                        $selectEmail = $bdd->prepare("SELECT * FROM users WHERE email = ?");
                        $selectEmail->execute([$emailRegistration]);
                        // counts the number of times login and email is in the database
                        $countLogin = $selectLogin->rowCount();
                        $countEmail = $selectEmail->rowCount();

                        // check that user is not registered
                        if ($countLogin !== 0 && $countEmail !== 0) {
                            $error = "Vous êtes déja inscrit";
                        }
                        else{

                            // check login is not used
                            if ($countLogin === 0) {

                                // check that user is not used
                                if ($countEmail === 0) {

                                    // password hash
                                    $passwordRegistrationHash = password_hash($passwordRegistration, PASSWORD_BCRYPT);

                                    $insert = $bdd->prepare("INSERT INTO users(login, email, password) VALUES(?,?,?)");
                                    $insert->execute([$loginRegistration, $emailRegistration, $passwordRegistrationHash]);
                                    $registered= "Bienvenue ";

                                    // retrieves idUser in database
                                    $selectIdUser = $bdd->prepare("SELECT idUser FROM users WHERE login = ?");
                                    $selectIdUser->execute([$loginRegistration]);
                                    $selectIdUser = $selectIdUser->fetch();

                                    // creation of a cookie ($_session['name cookie']) where we store iduser connected
                                    $idUser = $selectIdUser["idUser"];
                                    $_SESSION['idUser'] = $idUser;
                                }
                                else{
                                    $error = "Email déja utilisé merci de saisir un autre";
                                }
                            }
                            else{
                                $error = "Identifiant déja utilisé merci de saisir un autre";
                            }
                        }
                    }
                    else{
                        $error = "Les mots de passes ne sont pas identiques";
                    }
                }
                else{
                    $error = "Veuillez entrer un email valide";
                }
            }
            else{
                $error = "Veuillez remplir les champs";
            }
        }  

    // ----------------------------------------------------------

    // ------------------ CONNECT -------------------------------

        if (isset($_POST["button-connect"])) {

            // retrieves input login , email , password and confirm password values
            $loginConnect = $_POST["login-connect"];
            $passwordConnect = $_POST["password-connect"];

            // check that user fills all input
            if (!empty($loginConnect) && !empty($passwordConnect)) {

                // send a request to mysql to check if the login exists
                $selectLogin = $bdd->prepare("SELECT * FROM users WHERE login = ?");
                $selectLogin->execute([$loginConnect]);

                $countLogin = $selectLogin->rowCount();
    
                // check login exist in database
                if ($countLogin != 0) {

                    // retrieves the encrypted password with the validated login
                    $selectPasswordConnectHash = $bdd->prepare("SELECT password FROM users WHERE login = ?");
                    $selectPasswordConnectHash->execute([$loginConnect]);
                    $selectPasswordConnectHash = $selectPasswordConnectHash->fetch();
                    $passwordConnectHash = $selectPasswordConnectHash['password'];

                    // checks that the password entered is identical to the encrypted password of the login
                    if (password_verify($passwordConnect, $passwordConnectHash)) {
                        $connect = "Bon retour parmis nous ";

                        // retrieves idUser in database
                        $selectIdUser = $bdd->prepare("SELECT idUser FROM users WHERE login = ?");
                        $selectIdUser->execute([$loginConnect]);
                        $selectIdUser = $selectIdUser->fetch();

                        // creation of a cookie ($_session['name cookie']) where we store iduser connected
                        $idUser = $selectIdUser["idUser"];
                        $_SESSION['idUser'] = $idUser;
                    }
                    else{
                        $error = "Votre mot de passe est incorrect";
                    }
                }
                else{
                    $error = "Votre identifiant est incorrect";
                }
            }
            else{
                $error = "Veuillez remplir les champs";
            }
        }

    // ----------------------------------------------------------

    //--------------------TODOLIST add/modify/delete--------------------------------

        // --------------add todolist------------------
            if (isset($_POST["button-add"]) && isset($_SESSION['idUser'])) {

                // retrieves input name-todolist values
                $nameTodolist = $_POST["name-todolist"];

                if (!empty($nameTodolist)) {
             
                    // insert todolist to database
                    $insert = $bdd->prepare("INSERT INTO todolist(nameTodolist, state, idUser) VALUES(?,?,?)");
                    $insert->execute([$nameTodolist, "not done", $_SESSION["idUser"]]);
                }
                else{
                    $error = "Veuillez remplir le champs";
                }
            }
            else{
                $error = "Connectez vous ou créer un compte.";
            }
        // ----------------modify state----------------
            
            if (isset($_POST['button-modify'])) {
                
                // retrieves all data todolist
                $selectTodolist = $bdd->prepare("SELECT * FROM todolist WHERE idTodolist = ?");
                $selectTodolist->execute([$_POST['button-modify']]);
                $todolist = $selectTodolist->fetchAll();
            }

            if (isset($_POST['registered-modify'])) {

                // retrieves name, state and idTodolist with input values
                $nameModify = $_POST["name-modify"];
                $stateModify = $_POST["state-modify"];
                $test = $_POST["registered-modify"];

                if (!empty($nameModify) && !empty($stateModify)) {

                    // update data with new values 
                    $update = $bdd->prepare("UPDATE todolist SET nameTodolist= ?, state= ? WHERE idTodolist= ?");
                    $update->execute([$nameModify, $stateModify, $test]);
                }
                else{
                    $error = "Veuillez remplir le champs";
                }
            }
        // ----------------delete todolist----------------
            if (isset($_POST['button-delete'])) {
                $selectTodolist = $bdd->prepare("DELETE FROM todolist WHERE idTodolist = ?");
                $selectTodolist->execute([$_POST['button-delete']]); // corresponds to idTodolist of the selected todolist
            }
    // ----------------------------------------------------------------

    //--------------------DISCONNECT---------------------------------
        if (isset($_POST["button-disconnect"])) {

            // empties the cookies and destroy session
            $_SESSION = [];
            session_destroy();
        }
    //-----------------------------------------------------------
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
                    echo "  <form action='' method='post'>
                                <button type='submit' name='button-disconnect' class='button-disconnect'>Déconnexion</button>
                            </form>";
                }
            ?>
        <!-- ---------------------------------------------------------------- -->
    </div>

    <!-- ----------------CONTAINER FORM-------------------- -->
        <?php
            if (isset($_SESSION['idUser'])) {
            
            }
            else{ 
                echo"   <div class='container-form'>
                        <!-- -------------- CONNECT FORM----------------------- -->
                            <div class='connect-registration'>
                                <h1>Connexion</h1>
                                <form action='' method='post' class='form-registered-connect'>
                                    <input type='text' name='login-connect' placeholder='Identifiant'>
                                    <input type='text' name='password-connect' placeholder='Mot de passe'>
                                    <button type='submit' name='button-connect'>Connecte-toi !</button>
                                </form>
                            </div>
                        <!-- ---------------------------------------------- -->
                        <!-- --------------REGISTRATION FORM----------------------- -->
                            <div class='connect-registration'>
                                <h1>Inscription</h1>
                                <form action='' method='post' class='form-registered-connect'>
                                    <input type='text' name='login-registration' placeholder='Identifiant'>
                                    <input type='email' name='email-registration' placeholder='Email'>
                                    <input type='text' name='password-registration' placeholder='Mot de passe'>
                                    <input type='text' name='confirm-password-registration' placeholder='Confirmer mot de passe'>
                                    <button type='submit' name='button-registration'>Inscris-toi !</button>
                                </form>
                            </div>
                        <!-- ---------------------------------------------- -->
                        </div>";
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








