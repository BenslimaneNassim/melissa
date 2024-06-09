<?php
include 'connection.php';
session_start();
try {
    $myproject = new PDO("mysql:host=localhost;dbname=myproject;charset=utf8", "root", "");
    $myproject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo ('<p style="color: green;">Connexion à la base de données réussie.</p>');
} catch (PDOException $e) {
    die('<p style="color: red;">Erreur de connexion à la base de données : ' . $e->getMessage() . '</p>');
}
// Vérifier si le nom de l'enseignant est défini dans la session
if (isset($_SESSION['nom']) && !empty($_SESSION['nom'])) {
    $nom_enseignant = $_SESSION['nom'];


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Récupérer les autres données du formulaire
        $jour = $_POST["jour"];
        //Recuperer l'id de la plage horaire 
        $idPlageHoraire = $_POST["horaire_id"];
        // Récupérer les heures de début et de fin correspondantes depuis la table "Horaire"
        $sqlHoraire = "SELECT debut, fin FROM Horaire WHERE id = ?";
        $stmtHoraire = mysqli_prepare($con, $sqlHoraire);
        mysqli_stmt_bind_param($stmtHoraire, "i", $idPlageHoraire);
        mysqli_stmt_execute($stmtHoraire);
        mysqli_stmt_bind_result($stmtHoraire, $heureDebut, $heureFin);
        mysqli_stmt_fetch($stmtHoraire);
        mysqli_stmt_close($stmtHoraire);

        // Insérer la disponibilité dans la table enseignant_disponibilite avec le nom de l'enseignant connecté
        $sqlInsert = "INSERT INTO Enseignant_Disponibilite (nom_enseignant, jour, heureDebut, heureFin) VALUES (?,?,?,?)";

        // Préparer la déclaration
        $stmtInsert = mysqli_prepare($con, $sqlInsert);

        if ($stmtInsert) {
            // Lier les paramètres
            mysqli_stmt_bind_param($stmtInsert, "ssss", $nom_enseignant, $jour, $heureDebut, $heureFin);

            // Exécuter la déclaration
            if (mysqli_stmt_execute($stmtInsert)) {
                header("Location: createDispo.php?msg=success");
                exit();
                // Disponibilité ajoutée avec succès
                // echo "Disponibilité ajoutée avec succès.";
            } else {
                // Erreur lors de l'ajout de la disponibilité
                echo "Erreur : " . mysqli_error($con);
            }

            // Fermer la déclaration
            mysqli_stmt_close($stmtInsert);
        } else {
            echo "Erreur de préparation de la requête : " . mysqli_error($con);
        }
    }
} else {
    echo "Nom de l'enseignant non trouvé dans la session.";
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <title>Ajout Disponibilités</title>
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

        <div class=" col-md-6">

            <div class="form-container">
                <a href="dispo.php" class="arrow"><span class="material-symbols-outlined">
                        arrow_back
                    </span></a>
                <style>
                    .arrow {
                        color: black;
                    }
                </style>
                <?php
                if (isset($_GET['msg']) && $_GET['msg'] === 'success') {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                     <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                        Ajout réussi
                     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                       </div>';
                }
                ?>

                <h2 class="text-center mb-4">Ajouter vos Disponibilités</h2>

                <form method="post">
                    <div class="mb-3">
                        <label class="col-sm-4 col-form-label">Nom </label>
                        <input type="text" class="form-control" name="nom" value="<?php echo isset($_SESSION['nom']) ? $_SESSION['nom'] : ''; ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="col-sm-4 col-form-label">Jour</label>
                        <input type="date" class="form-control" name="jour" value="">
                    </div>


                    <div class="mb-3">
                        <label class="col-sm-4 col-form-label">Plage Horaire</label>
                        <select class="form-select" name="horaire_id">
                            <option selected disabled>Choisir une plage horaire</option>
                            <?php
                            // Récupérer les plages horaires disponibles depuis la table Horaire
                            $sql = "SELECT id, CONCAT(debut, ' - ', fin) AS plage_horaire FROM Horaire";
                            $result = mysqli_query($con, $sql);
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='{$row['id']}'>{$row['plage_horaire']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>



                    <div class="row">
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-primary w-100">Ajouter</button>
                        </div>
                        <div class="col-sm-6">
                            <a class="btn btn-outline-secondary w-100" href="dispo.php" role="button">Annuler</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>