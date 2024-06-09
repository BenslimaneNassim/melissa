<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="assets/css/etudiant.css">
    <title>Créer un groupe</title>
    <style>
        /* Vos styles CSS ici */
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
<body>
    <!-- Votre contenu HTML et formulaire de création de groupe ici -->
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="col-md-6">
            <div class="form-container">
                <h2 class="text-center mb-4">Ajouter un Groupe</h2>
                <br>
                <?php
                $error_message = '';

                try {
                    $myproject = new PDO("mysql:host=localhost;dbname=myproject; charset=utf8;", "root", "");
                    $myproject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    $error_message = '<p class="text-danger">Erreur de connexion à la base de données : ' . $e->getMessage() . '</p>';
                }

                if (isset($_POST['submit'])) {
                    // Récupération des données du formulaire
                    $nom_groupe = $_POST['nom_groupe'];
                    $section = $_POST['section'];
                    $nom_specialite = $_POST['nom_specialite'];
                    $nombre_etudiants = $_POST['nombre_etudiants'];

                    // Vérification de l'existence de la combinaison de clé primaire
                    $sql_check = "SELECT COUNT(*) AS count FROM groupe WHERE nom_groupe = :nom_groupe AND section = :section AND nom_specialite = :nom_specialite";
                    $stmt_check = $myproject->prepare($sql_check);
                    $stmt_check->bindParam(':nom_groupe', $nom_groupe);
                    $stmt_check->bindParam(':section', $section);
                    $stmt_check->bindParam(':nom_specialite', $nom_specialite);
                    $stmt_check->execute();
                    $row_check = $stmt_check->fetch(PDO::FETCH_ASSOC);
                    if ($row_check['count'] > 0) {
                        $error_message = '<p class="text-danger">Erreur : La combinaison de clés primaires existe déjà.</p>';
                    } else {
                        try {
                            $stmt = $myproject->prepare("INSERT INTO groupe (nom_groupe, section, nom_specialite, nombre_etudiants) VALUES (?, ?, ?, ?)");
                            $stmt->execute([$nom_groupe, $section, $nom_specialite, $nombre_etudiants]);
                            echo '<p class="text-success">Données insérées avec succès.</p>';
                        } catch (PDOException $e) {
                            $error_message = '<p class="text-danger">Erreur lors de l\'insertion des données : ' . $e->getMessage() . '</p>';
                        }
                    }
                }
                ?>

                <form method="post" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Groupe :</label>
                        <div class="col-sm-9">
                            <input class="form-control" name="nom_groupe" placeholder="Le Nom du groupe">

                        </div>
                    </div>
                    <br>
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Spécialité :</label>
                        <div class="col-sm-9">
                            <input class="form-control" name="nom_specialite" placeholder="le nom de la specialite">

                        </div>
                    </div>
                    <br>



                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Section :</label>
                        <div class="col-sm-9">
                            <select class="form-select" name="section">
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <!-- Ajoutez d'autres options si nécessaire -->
                            </select>
                        </div>
                    </div>
                    <br>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Nbr Etudiants :</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" name="nombre_etudiants">
                        </div>
                    </div>
                    <br>


                    <div class="row">
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-primary w-100" name="submit">Ajouter</button>
                        </div>
                        <div class="col-sm-6">
                            <a class="btn btn-outline-primary w-100" href="etudiant.php" role="button">Retourner</a>
                        </div>
                    </div>
                </form>
                <?php echo $error_message; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

</body>

</html>