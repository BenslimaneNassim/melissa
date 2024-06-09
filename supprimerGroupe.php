<?php
// Vérifie si les informations du groupe à supprimer sont présentes dans l'URL et ne sont pas vides
if (isset($_GET['nom_groupe']) && !empty($_GET['nom_groupe']) && isset($_GET['section']) && !empty($_GET['section']) && isset($_GET['nom_specialite']) && !empty($_GET['nom_specialite'])) {
    try {
        // Connexion à la base de données
        $myproject = new PDO("mysql:host=localhost;dbname=myproject;charset=utf8", "root", "");
        $myproject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Préparation de la requête SQL pour supprimer le groupe
        $stmt = $myproject->prepare("DELETE FROM groupe WHERE nom_groupe = :nom_groupe AND section = :section AND nom_specialite = :nom_specialite");
        
        // Liaison des valeurs et exécution de la requête
        $stmt->bindParam(':nom_groupe', $_GET['nom_groupe']);
        $stmt->bindParam(':section', $_GET['section']);
        $stmt->bindParam(':nom_specialite', $_GET['nom_specialite']);
        $stmt->execute();
        
        // Redirection vers une autre page après la suppression
        header("Location: etudiant.php");
        exit();
    } catch (PDOException $e) {
        // Gestion des erreurs PDO
        die('<p style="color: red;">Erreur lors de la suppression du groupe : ' . $e->getMessage() . '</p>');
    }
} else {
    // Si les informations nécessaires ne sont pas présentes dans l'URL ou sont vides, redirige vers la page précédente
    header("Location: etudiant.php");
    exit();
}
?>
