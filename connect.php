<?php 
    // authorized cookie and created session
    session_start();


    // connect to mysql
        try{
            $bdd = new PDO("mysql:host=localhost;dbname=motormemory", "root", "");
        }catch(PDOException $e) {
            echo "message:" . $e->getMessage();
        }
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
                    header("Location: index.php");

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
";

?>