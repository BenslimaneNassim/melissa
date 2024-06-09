<?php
// Initialisez la session
session_start();

$error_message = '';

try {
    $myproject = new PDO("mysql:host=localhost;dbname=myproject; charset=utf8;", "root", "");
    $myproject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $error_message = '<p class="text-danger">Erreur de connexion à la base de données : ' . $e->getMessage() . '</p>';
}

$nom_specialite = $_GET['nom_specialite']; // Utilisez 'id' au lieu de 'updateid'
$sql = "SELECT * FROM specialite WHERE nom_specialite = :nom_specialite"; // Utilisez une requête préparée pour éviter les injections SQL
$stmt = $myproject->prepare($sql);
$stmt->bindParam(':nom_specialite', $nom_specialite);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
// Vérifiez si une ligne a été trouvée
if (!$row) {
    echo "La Spécialité n'a pas été trouvée.";
    exit; 
}


$nom_specialite = $row['nom_specialite'];
$faculte = $row['faculte'];


// Vérifiez si le formulaire a été soumis
if (isset($_POST['submit'])) {
    // Récupérez les données soumises du formulaire
    $new_specialite = $_POST['new_nom_specialite']; // Nouvelle variable pour stocker le nouveau nom
    $faculte = $_POST['faculte'];
 
    
    // Vérifiez si la spécialité est déjà associée à un module
    $sql_check_module = "SELECT COUNT(*) AS module_count FROM module WHERE nom_specialite = :nom_specialite";
    $stmt_check_module = $myproject->prepare($sql_check_module);
    $stmt_check_module->bindParam(':nom_specialite', $nom_specialite);
    $stmt_check_module->execute();
    $result_check_module = $stmt_check_module->fetch(PDO::FETCH_ASSOC);

    if ($result_check_module['module_count'] > 0) {
        $error_message = "Impossible de modifier le nom de la spécialité car elle est déjà associée à un module.";
    } else {
        // Mettez à jour les informations de la spécialité dans la base de données
        $sql_update_specialite = "UPDATE specialite SET nom_specialite = :new_nom_specialite, faculte = :faculte WHERE nom_specialite = :nom_specialite";
        $stmt_update_specialite = $myproject->prepare($sql_update_specialite);
        $stmt_update_specialite->bindParam(':new_nom_specialite', $new_specialite);
        $stmt_update_specialite->bindParam(':faculte', $faculte);
        $stmt_update_specialite->bindParam(':nom_specialite', $nom_specialite);
        $stmt_update_specialite->execute();

        // Stockez le message de succès dans une variable de session
        $_SESSION['success_message'] = "Modification effectuée avec succès.";

        // Redirigez l'utilisateur vers une autre page après la mise à jour si nécessaire
        // header('Location: page_apres_modification.php');
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edite Spécialité</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        /* Styles CSS personnalisés */
        .form-container {
            background-color: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 0.5s ease forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="col-md-6">
            <div class="form-container">
                <h2 class="text-center mb-4">Editer une Spécialités</h2>
                <!-- Affichez le message de succès s'il existe -->

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $_SESSION['success_message']; ?>
                    </div>
                    <?php unset($_SESSION['success_message']); // Supprimez le message de la session après l'affichage  ?>
                <?php endif; ?>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" enctype="multipart/form-data">
                    
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Intitulé :</label>
                        <div class="col-sm-8">
                        <input type="text" class="form-control" name="new_nom_specialite"
                            placeholder="Le Nouveau Nom de la specialite"
                            value="<?php echo htmlspecialchars($new_specialite ?? $nom_specialite); ?>">                        </div>
                    </div>
                    <br>

                    

                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">La faculté :</label>
                        <div class="col-sm-8">
                            <input class="form-control" name="faculte" value="<?php echo $faculte; ?>">

                        </div>
                    </div>
                    <br>
                    

                    <div class="row">
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-primary w-100" name="submit">Modifier</button>
                        </div>
                        <div class="col-sm-6">
                            <a class="btn btn-outline-primary w-100" href="module.php" role="button">Retourner</a>
                        </div>
                    </div>
                </form>
                
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>
