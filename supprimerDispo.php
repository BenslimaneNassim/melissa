<?php
require 'connection.php';
session_start();


// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['nom'])) {
    // Récupérer le nom de l'utilisateur connecté
    $nom = $_SESSION['nom'];

    // Vérifiez si l'ID à supprimer est présent dans l'URL
    if (isset($_GET['id'])) {
        // Récupérez l'ID de l'enregistrement à supprimer
        $id = $_GET['id'];

        // Écrivez la requête SQL pour supprimer l'enregistrement
        $sql = "DELETE FROM enseignant_disponibilite WHERE id = ?";

        // Préparez la déclaration SQL
        $stmtDelete = mysqli_prepare($con, $sql);

        if ($stmtDelete === false) {
            die('Erreur de préparation de la déclaration : ' . mysqli_error($con));
        }

        // Lier les paramètres i pour entier
        mysqli_stmt_bind_param($stmtDelete, "i", $id);

        // Exécuter la déclaration
        if (mysqli_stmt_execute($stmtDelete)) {
            // Redirigez vers la page principale après la suppression
            header("Location: dispo.php?msg=deleted");
            exit();
        } else {
            // Gérer les erreurs
            echo "Erreur d'exécution : " . mysqli_stmt_error($stmtDelete);
        }

        // Fermez la déclaration
        mysqli_stmt_close($stmtDelete);
    } else {
        echo "ID non spécifié.";
    }
} else {
    header("location:login.php?error");
    exit();
}
