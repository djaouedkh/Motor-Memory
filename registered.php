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
                                header("Location: index.php");
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
echo"
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

?>