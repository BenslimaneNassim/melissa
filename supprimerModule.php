<?php
// Vérifie si l'identifiant du module à supprimer est présent dans l'URL
if (isset($_GET['id_module']) && !empty($_GET['id_module'])) {
    try {
        $myproject = new PDO("mysql:host=localhost;dbname=myproject;charset=utf8", "root", "");
        $myproject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       
        $stmt = $myproject->prepare("DELETE FROM module WHERE id_module = :id_module");
        
        // Lie les valeurs et exécute la requête
        $stmt->bindParam(':id_module', $_GET['id_module']);
        $stmt->execute();
        
        // Redirige vers la page des modules après la suppression
        header("Location: module.php");
        exit();
    } catch (PDOException $e) {
        die('<p style="color: red;">Erreur lors de la suppression du module : ' . $e->getMessage() . '</p>');
    }
} else {
    
    header("Location: module.php");
    exit();
}
?>