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

$id_module = $_GET['id_module']; // Utilisez 'id' au lieu de 'updateid'
$sql = "SELECT * FROM module WHERE id_module = :id_module"; // Utilisez une requête préparée pour éviter les injections SQL
$stmt = $myproject->prepare($sql);
$stmt->bindParam(':id_module', $id_module);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
// Vérifiez si une ligne a été trouvée
if (!$row) {
    echo "Le module n'a pas été trouvé.";
    exit; // Arrêtez l'exécution du script si le module n'est pas trouvé
}

// Pré-remplissez les variables avec les données du module
$nom_module = $row['nom_module'];
$activite = $row['activite'];
$nom_specialite = $row['nom_specialite'];
$charge_module = $row['charge_module'];

// Vérifiez si le formulaire a été soumis
if (isset($_POST['submit'])) {
    // Récupérez les données soumises du formulaire
    $id_module = $_POST['id_module'];
    $nom_module = $_POST['nom_module'];
    $activite = $_POST['activite'];
    $nom_specialite = $_POST['nom_specialite'];
    $charge_module = $_POST['charge_module'];

    // Vérification de l'existence de nom_specialite
    $stmt_specialite = $myproject->prepare("SELECT COUNT(*) AS count FROM specialite WHERE nom_specialite = ?");
    $stmt_specialite->execute([$nom_specialite]);
    $specialite_count = $stmt_specialite->fetchColumn();

    if ($specialite_count == 0) {
        // Affichez un message d'erreur ou effectuez une action appropriée si la spécialité n'existe pas
        $error_message .= '<p class="text-danger">La spécialité saisie n\'existe pas.</p>';
    }

    // Vérification de l'existence de charge_module
    $stmt_charge_module = $myproject->prepare("SELECT COUNT(*) AS count FROM enseignant WHERE nom = ?");
    $stmt_charge_module->execute([$charge_module]);
    $charge_module_count = $stmt_charge_module->fetchColumn();

    if ($charge_module_count == 0) {
        // Affichez un message d'erreur ou effectuez une action appropriée si le chargé de module n'existe pas
        $error_message .= '<p class="text-danger">Le chargé de module saisi n\'existe pas.</p>';
    }

    // S'il n'y a pas d'erreur, procédez à la mise à jour des informations du module dans la base de données
    if (empty($error_message)) {
        $sql = "UPDATE module SET nom_module = :nom_module, activite = :activite, nom_specialite = :nom_specialite, charge_module = :charge_module WHERE id_module = :id_module";
        $stmt = $myproject->prepare($sql);
        $stmt->bindParam(':nom_module', $nom_module);
        $stmt->bindParam(':activite', $activite);
        $stmt->bindParam(':nom_specialite', $nom_specialite);
        $stmt->bindParam(':charge_module', $charge_module);
        $stmt->bindParam(':id_module', $id_module);
        $stmt->execute();

        // Stockez le message de succès dans une variable de session
        $_SESSION['success_message'] = "Modification effectuée avec succès.";

        // Redirigez l'utilisateur vers une autre page après la mise à jour si nécessaire
        // header('Location: salle.php');
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Module</title>
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
                <h2 class="text-center mb-4">Editer un Module</h2>

                <!-- Affichez le message de succès s'il existe -->
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $_SESSION['success_message']; ?>
                    </div>
                    <?php unset($_SESSION['success_message']); // Supprimez le message de la session après l'affichage   ?>
                <?php endif; ?>

                <!-- Affichez les messages d'erreur s'il y en a -->
                <?php echo $error_message; ?>

                <form method="post" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label class="form-label">Identifiant :</label>
                        <input type="text" class="form-control" name="id_module" placeholder="L'id du module"
                            value="<?php echo $id_module; ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">L'Intitulé du module :</label>
                        <input class="form-control" name="nom_module" value="<?php echo $nom_module; ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Activité :</label>

                        <select class="form-select" name="activite">
                            <option value="Cours + TD + TP" <?php if ($activite == 'Cours + TD + TP')
                                echo 'selected'; ?>>
                                Cours + TD + TP</option>
                            <option value="Cours + TD" <?php if ($activite == 'Cours + TD')
                                echo 'selected'; ?>>Cours + TD
                            </option>
                            <option value="Cours + TP" <?php if ($activite == 'Cours + TP')
                                echo 'selected'; ?>>Cours + TP
                            </option>
                            <option value="TD + TP" <?php if ($activite == 'TD + TP')
                                echo 'selected'; ?>>TD + TP</option>
                            <option value="Cours" <?php if ($activite == 'Cours')
                                echo 'selected'; ?>>Cours</option>
                            <option value="TD" <?php if ($activite == 'TD')
                                echo 'selected'; ?>>TD</option>

                        </select>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">la Spécialité :</label>
                        <div class="col-sm-8">
                            <input class="form-control" name="nom_specialite" value="<?php echo $nom_specialite; ?>">

                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Chargé de module :</label>
                        <div class="col-sm-8">
                            <input class="form-control" name="charge_module" value="<?php echo $charge_module; ?>">

                        </div>
                    </div>

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
