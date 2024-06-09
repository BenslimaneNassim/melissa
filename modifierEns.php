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

$nom = $_GET['nom']; // Utilisez 'id' au lieu de 'updateid'
$sql = "SELECT * FROM enseignant WHERE nom = :nom"; // Utilisez une requête préparée pour éviter les injections SQL
$stmt = $myproject->prepare($sql);
$stmt->bindParam(':nom', $nom);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
// Vérifiez si une ligne a été trouvée
if (!$row) {
    echo "L'enseignant n'a pas été trouvé.";
    exit; // Arrêtez l'exécution du script si l'enseignant n'est pas trouvé
}

// Pré-remplissez les variables avec les données de l'enseignant
$nom = $row['nom'];
$code = $row['code'];
$email = $row['email'];
$grade = $row['grade'];

// Vérifiez si le formulaire a été soumis
if (isset($_POST['submit'])) {
    // Vérifiez d'abord si l'enseignant est responsable d'un module
    $sql_module = "SELECT * FROM module WHERE charge_module = :nom";
    $stmt_module = $myproject->prepare($sql_module);
    $stmt_module->bindParam(':nom', $nom);
    $stmt_module->execute();
    $module_row = $stmt_module->fetch(PDO::FETCH_ASSOC);
    if ($module_row) {
        // Si l'enseignant est responsable d'un module, affichez un message d'erreur
        $_SESSION['error_message'] = "Impossible de modifier le nom de l'enseignant car il est responsable d'un module.";
    } else {
        // Récupérez les données soumises du formulaire
        $new_nom_enseignant = $_POST['new_nom']; // Nouvelle variable pour stocker le nouveau nom
        $code = $_POST['code'];
        $email = $_POST['email'];
        $grade = $_POST['grade'];

        // Mettez à jour les informations de l'enseignant dans la base de données
        $sql = "UPDATE enseignant SET nom = :new_nom, code = :code, email = :email, grade = :grade WHERE nom = :nom";
        $stmt = $myproject->prepare($sql);
        $stmt->bindParam(':new_nom', $new_nom_enseignant);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':grade', $grade);
        $stmt->bindParam(':nom', $nom);
        $stmt->execute();

        // Mettre à jour $nom avec la nouvelle valeur
        $nom = $new_nom_enseignant;

        // Stockez le message de succès dans une variable de session
        $_SESSION['success_message'] = "Modification effectuée avec succès.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Enseignant</title>
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
                <h2 class="text-center mb-4">Editer un Enseignant</h2>

                <!-- Affichez le message d'erreur s'il existe -->
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['error_message']; ?>
                    </div>
                    <?php unset($_SESSION['error_message']); // Supprimez le message de la session après l'affichage ?>
                <?php endif; ?>

                <!-- Affichez le message de succès s'il existe -->
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $_SESSION['success_message']; ?>
                    </div>
                    <?php unset($_SESSION['success_message']); // Supprimez le message de la session après l'affichage ?>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data">

                   <div class="mb-3">
                        <label class="form-label">Nom et Prenom :</label>
                        <input type="text" class="form-control" name="new_nom"
                            placeholder="Le Nouveau Nom et Prenom de l'enseignant"
                            value="<?php echo htmlspecialchars($new_nom_enseignant ?? $nom); ?>">
                    </div>
                    

                    <!-- <div class="mb-3">
                        <label class="form-label">Nom et Prenom :</label>
                        <input class="form-control" name="nom" placeholder="Le Nom et Prenom de L'enseignant" value="<?php echo $nom; ?>">
                    </div> -->
                    <div class="mb-3">
                        <label class="form-label">Code :</label>
                        <input class="form-control" name="code" placeholder="Le Code de L'enseignant"
                            value="<?php echo $code; ?>">
                    </div>


                    <div class="mb-3">
                        <label class="form-label">Email :</label>
                        <input class="form-control" name="email" placeholder="@univ-bejaia.dz"
                            value="<?php echo $email; ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Grade</label>
                        <select class="form-select" name="grade">
                            <option value="MCA" <?php if ($grade == 'MCA')
                                echo 'selected'; ?>>MCA</option>
                            <option value="MCB" <?php if ($grade == 'MCB')
                                echo 'selected'; ?>>MCB</option>
                            <option value="Pr" <?php if ($grade == 'Pr')
                                echo 'selected'; ?>>Pr</option>
                            <option value="MAA" <?php if ($grade == 'MAA')
                                echo 'selected'; ?>>MAA</option>
                            <option value="MAB" <?php if ($grade == 'MAB')
                                echo 'selected'; ?>>MAB</option>
                            <option value="Doct" <?php if ($grade == 'Doct')
                                echo 'selected'; ?>>Doct</option>
                            <option value="Vacataire" <?php if ($grade == 'Vacataire')
                                echo 'selected'; ?>>Vacataire
                            </option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-primary w-100" name="submit">Modifier</button>
                        </div>
                        <div class="col-sm-6">
                            <a class="btn btn-outline-primary w-100" href="enseignant.php" role="button">Retourner</a>
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
