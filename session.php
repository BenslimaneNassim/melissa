<?php
session_start();

// Vérifie si l'utilisateur est connecté
// if (!isset($_SESSION['admin_logged_in'])) {
//     header("location: login.php");
//     exit();
// }

// Déconnexion de l'utilisateur
if (isset($_GET['logout'])) {
    session_unset(); // Efface toutes les variables de session
    session_destroy(); // Détruit la session
    header("location: login.php");
    exit();
}
