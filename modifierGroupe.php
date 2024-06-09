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

$nom_groupe = $_GET['nom_groupe'];
$section = $_GET['section'];
$nom_specialite = $_GET['nom_specialite'];
$sql = "SELECT * FROM groupe WHERE nom_groupe = :nom_groupe AND section = :section AND nom_specialite = :nom_specialite";
$stmt = $myproject->prepare($sql);
$stmt->bindParam(':nom_groupe', $nom_groupe);
$stmt->bindParam(':section', $section);
$stmt->bindParam(':nom_specialite', $nom_specialite);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
// Vérifiez si une ligne a été trouvée
if (!$row) {
    echo "Le groupe n'a pas été trouvé.";
    exit; // Arrêtez l'exécution du script si le groupe n'est pas trouvé
}

// Pré-remplissez les variables avec les données du groupe
$nom_groupe = $row['nom_groupe'];
$section = $row['section'];
$nom_specialite = $row['nom_specialite'];
$nombre_etudiants = $row['nombre_etudiants'];

// Vérifiez si le formulaire a été soumis
if (isset($_POST['submit'])) {
    // Récupérez les données soumises du formulaire
    $nom_groupe = $_POST['nom_groupe'];
    $section = $_POST['section'];
    $nom_specialite = $_POST['nom_specialite'];
    $nombre_etudiants = $_POST['nombre_etudiants'];

    // Mettez à jour les informations du groupe dans la base de données
    $sql = "UPDATE groupe SET nom_groupe = :nom_groupe, section = :section, nom_specialite = :nom_specialite, nombre_etudiants = :nombre_etudiants WHERE nom_groupe = :old_nom_groupe AND section = :old_section AND nom_specialite = :old_nom_specialite";
    $stmt = $myproject->prepare($sql);
    $stmt->bindParam(':nom_groupe', $nom_groupe);
    $stmt->bindParam(':section', $section);
    $stmt->bindParam(':nom_specialite', $nom_specialite);
    $stmt->bindParam(':nombre_etudiants', $nombre_etudiants);
    $stmt->bindParam(':old_nom_groupe', $_GET['nom_groupe']);
    $stmt->bindParam(':old_section', $_GET['section']);
    $stmt->bindParam(':old_nom_specialite', $_GET['nom_specialite']);
    $stmt->execute();

    // Vérifiez le nombre de lignes affectées par la requête UPDATE
    if ($stmt->rowCount() > 0) {
        // Stockez le message de succès dans une variable de session
        $_SESSION['success_message'] = "Modification effectuée avec succès.";
    } else {
        // Stockez le message d'erreur dans une variable de session
        $_SESSION['error_message'] = "Erreur : La combinaison de clés primaires existe déjà.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Groupe</title>
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
                <h2 class="text-center mb-4">Editer un Groupe</h2>

                <!-- Affichez le message de succès s'il existe -->
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $_SESSION['success_message']; ?>
                    </div>
                    <?php unset($_SESSION['success_message']); // Supprimez le message de la session après l'affichage ?>
                <?php endif; ?>

                <!-- Affichez le message d'erreur s'il existe -->
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['error_message']; ?>
                    </div>
                    <?php unset($_SESSION['error_message']); // Supprimez le message de la session après l'affichage ?>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Nom du Groupe :</label>
                        <input class="form-control" name="nom_groupe" value="<?php echo $nom_groupe; ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Spécialité :</label>
                        <input class="form-control" name="nom_specialite" value="<?php echo $nom_specialite; ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Section :</label>
                        <input class="form-control" name="section" value="<?php echo $section; ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre d'Étudiants :</label>
                        <input class="form-control" name="nombre_etudiants" value="<?php echo $nombre_etudiants; ?>">
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-primary w-100" name="submit">Modifier</button>
                        </div>
                        <div class="col-sm-6">
                            <a class="btn btn-outline-primary w-100" href="etudiant.php" role="button">Retourner</a>
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
