<?php
include('fonction.php');
$error = "";
echo 'URL appelée : '.$_SERVER['REQUEST_URI'].'<br>';
echo '<pre>';
print_r($_POST);
echo '</pre>';



if (!empty($_POST['email']) && !empty($_POST['mot_de_passe'])) {
    $username = $_POST['email'];
    $password = $_POST['mot_de_passe'];
    $row = login($username, $password);
    if ($row) {
        session_start();
        $_SESSION['id'] = $row['id'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['nom'] = $row['nom'];
        $_SESSION['prenom'] = $row['prenom'];
        $_SESSION['telephone'] = $row['telephone'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['updated'] = (bool)$row['updated'];
        if ($row['role'] == 'admin') {
            header('Location: ../public/appManager/series/home.php');
            exit();
        } 
        else if ($row['role'] == 'tournage') {
           header('Location: ../public/appManager/series/home.php');
            exit();
        }
        else if ($row['role'] == 'caisse') {
           header('Location: ../public/appManager/series/home.php');
            exit();
        }
        else if ($row['role'] == 'comptable') {
           header('Location: ../public/appManager/series/home.php');
            exit();
        }  else {
            header('Location: ../index');
            exit();
            }
        }
     else {
        $error_message = 'Incorrect username or password!';
        $error = "Nom d'utilisateur ou mot de passe Incorrect";
        header('Location: ../index.php?error=1');
        exit();
    }
}
else{
    echo"bonjours";
}