<?php
include('fonction.php');
$error = "";

if (!empty($_GET['email']) && !empty($_GET['mot_de_passe'])) {
    $username = $_GET['email'];
    $password = $_GET['mot_de_passe'];
    $row = login($username, $password);
    if ($row) {
        session_start();
        $_SESSION['id'] = $row['id'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['nom'] = $row['nom'];
        $_SESSION['prenom'] = $row['prenom'];
        $_SESSION['telephone'] = $row['telephone'];
        $_SESSION['role'] = $row['role'];
        if ($row['role'] == 'admin') {
            header('Location: ../pages/about-us.php');
            exit();
        } 
        else if ($row['role'] == 'manager') {
           header('Location: ../pages/about-us.php');
            exit();
        }  else {
            header('Location: ../index');
            exit();
            }
        }
     else {
        $error_message = 'Incorrect username or password!';
        $error = "Nom d'utilisateur ou mot de passe Incorrect";
        header('Location: /projet_suivi/?error=1');
        exit();
    }
}