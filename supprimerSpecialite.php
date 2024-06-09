<?php
// Vérifie si l'identifiant de la spécialité à supprimer est présent dans l'URL
if (isset($_GET['nom_specialite']) && !empty($_GET['nom_specialite'])) {
    try {
        $myproject = new PDO("mysql:host=localhost;dbname=myproject;charset=utf8", "root", "");
        $myproject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       
        // Vérifiez si la spécialité est associée à un module
        $sql_check_module = "SELECT COUNT(*) AS module_count FROM module WHERE nom_specialite = :nom_specialite";
        $stmt_check_module = $myproject->prepare($sql_check_module);
        $stmt_check_module->bindParam(':nom_specialite', $_GET['nom_specialite']);
        $stmt_check_module->execute();
        $result_check_module = $stmt_check_module->fetch(PDO::FETCH_ASSOC);

        if ($result_check_module['module_count'] > 0) {
            echo '<script>alert("Impossible de supprimer la spécialité car elle est déjà associée à un module.");</script>';
            echo '<script>window.location.href = "module.php";</script>';
            exit();
        } else {
            // Procédez à la suppression de la spécialité
            $stmt = $myproject->prepare("DELETE FROM specialite WHERE nom_specialite = :nom_specialite");
        
            // Lie les valeurs et exécute la requête
            $stmt->bindParam(':nom_specialite', $_GET['nom_specialite']);
            $stmt->execute();
        
            // Redirige vers la page des spécialités après la suppression
            header("Location: module.php");
            exit();
        }
    } catch (PDOException $e) {
        die('<p style="color: red;">Erreur lors de la suppression de la spécialité : ' . $e->getMessage() . '</p>');
    }
} else {
    header("Location: module.php");
    exit();
}
?>
