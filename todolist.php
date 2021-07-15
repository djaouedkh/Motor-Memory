<?php 


    // connect to mysql
        try{
            $bdd = new PDO("mysql:host=localhost;dbname=motormemory", "root", "");
        }catch(PDOException $e) {
            echo "message:" . $e->getMessage();
        }
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

?>