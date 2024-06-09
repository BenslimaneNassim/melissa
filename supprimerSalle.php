<?php
// Vérifie si le numero de la salle à supprimer est présent dans l'URL
if (isset($_GET['numero']) && !empty($_GET['numero'])) {
    try {
        $myproject = new PDO("mysql:host=localhost;dbname=myproject;charset=utf8", "root", "");
        $myproject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Prépare la requête SQL pour supprimer le lieu avec le numero spécifié
        $stmt = $myproject->prepare("DELETE FROM lieu WHERE numero = :numero");
        
        // Lie les valeurs et exécute la requête
        $stmt->bindParam(':numero', $_GET['numero']);
        $stmt->execute();
        
        // Redirige vers la page des salles après la suppression
        header("Location: salle.php");
        exit();
    } catch (PDOException $e) {
        die('<p style="color: red;">Erreur lors de la suppression de la salle : ' . $e->getMessage() . '</p>');
    }
} else {
    // Si le numero de la salle n'est pas présente dans l'URL, redirige vers la page des salles
    header("Location: salle.php");
    exit();
}
?>