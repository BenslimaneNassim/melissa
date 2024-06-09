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

$numero = $_GET['numero']; // Utilisez 'id' au lieu de 'updateid'
$sql = "SELECT * FROM lieu WHERE numero = :numero"; // Utilisez une requête préparée pour éviter les injections SQL
$stmt = $myproject->prepare($sql);
$stmt->bindParam(':numero', $numero);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
// Vérifiez si une ligne a été trouvée
if (!$row) {
    echo "Le lieu n'a pas été trouvé.";
    exit; // Arrêtez l'exécution du script si le lieu n'est pas trouvé
}

// Pré-remplissez les variables avec les données de la salle
$capacite = $row['capacite'];
$type_lieu = $row['type_lieu'];


// Vérifiez si le formulaire a été soumis
if (isset($_POST['submit'])) {
    // Récupérez les données soumises du formulaire
    $new_numero = $_POST['new_numero_salle'];
    $capacite = $_POST['capacite'];
    $type_lieu = $_POST['type_lieu'];


    // Mettez à jour les informations de la salle dans la base de données
    $sql = "UPDATE lieu SET numero = :new_numero_salle ,capacite = :capacite, type_lieu = :type_lieu WHERE numero = :numero";
    $stmt = $myproject->prepare($sql);
    $stmt->bindParam(':new_numero_salle', $new_numero);
    $stmt->bindParam(':capacite', $capacite);
    $stmt->bindParam(':type_lieu', $type_lieu);

    $stmt->bindParam(':numero', $numero);
    $stmt->execute();

    // Stockez le message de succès dans une variable de session
    $_SESSION['success_message'] = "Modification effectuée avec succès.";

    // Redirigez l'utilisateur vers une autre page après la mise à jour si nécessaire
    //header('Location: salle.php');
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
                <h2 class="text-center mb-4">Editer un Lieu</h2>

                <!-- Affichez le message de succès s'il existe -->
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $_SESSION['success_message']; ?>
                    </div>
                    <?php unset($_SESSION['success_message']); // Supprimez le message de la session après l'affichage   ?>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data">


                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Numero :</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="new_numero_salle" placeholder="Le numéro de la salle"
                                value="<?php echo htmlspecialchars($new_numero ?? $numero); ?>">
                        </div>
                    </div>
                    <br>
               


                    <div class="mb-3">
                        <label class="form-label">Capacité :</label>
                        <input class="form-control" name="capacite" placeholder="La capacite de la salle"
                            value="<?php echo $capacite; ?>">
                    </div>


                    <div class="mb-3">
                        <label class="form-label">Type de lieu :</label>
                        <input class="form-control" name="type_lieu" placeholder="Le Type de la salle"
                            value="<?php echo $type_lieu; ?>">
                    </div>


                    <div class="row">
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-primary w-100" name="submit">Modifier</button>
                        </div>
                        <div class="col-sm-6">
                            <a class="btn btn-outline-primary w-100" href="salle.php" role="button">Retourner</a>
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