<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Module</title>
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
                <h2 class="text-center mb-4">Ajouter un Module</h2>
                <?php
                $error_message = '';

                try {
                    $myproject = new PDO("mysql:host=localhost;dbname=myproject; charset=utf8;", "root", "");
                    $myproject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    $error_message = '<p class="text-danger">Erreur de connexion à la base de données : ' . $e->getMessage() . '</p>';
                }

                // Vérifie si le formulaire a été soumis
                if (isset($_POST['submit'])) {
                    try {
                        $myproject = new PDO("mysql:host=localhost;dbname=myproject; charset=utf8;", "root", "");
                        $myproject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        // Récupérez les données soumises du formulaire
                        $id_module = $_POST['id_module'];
                        $nom_module = $_POST['nom_module'];
                        $activite = $_POST['activite'];
                        $nom_specialite = $_POST['nom_specialite'];
                        $charge_module = $_POST['charge_module'];

                        // Vérifiez si un module avec le même identifiant existe déjà
                        $stmt_check_existence = $myproject->prepare("SELECT COUNT(*) AS count FROM module WHERE id_module = :id_module");
                        $stmt_check_existence->bindParam(':id_module', $id_module);
                        $stmt_check_existence->execute();
                        $result_check_existence = $stmt_check_existence->fetch(PDO::FETCH_ASSOC);

                        if ($result_check_existence['count'] > 0) {
                            echo '<script>alert("Un module avec le même identifiant existe déjà.");</script>';
                        } else {
                            // Insérez le module dans la base de données
                            $stmt_insert_module = $myproject->prepare("INSERT INTO module (id_module, nom_module, activite, nom_specialite, charge_module) VALUES (?, ?, ?, ?, ?)");
                            $stmt_insert_module->execute([$id_module, $nom_module, $activite, $nom_specialite, $charge_module]);
                            echo '<script>alert("Module ajouté avec succès."); window.location.href = "module.php";</script>';
                            exit();
                        }
                    } catch (PDOException $e) {
                        echo '<p class="text-danger">Erreur lors de l\'insertion des données : ' . $e->getMessage() . '</p>';
                    }
                }
                ?>
                <form method="post" enctype="multipart/form-data">

                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Identifiant :</label>
                        <div class="col-sm-8">
                            <input class="form-control" name="id_module">

                        </div>
                    </div>
                    <br>

                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Intitulé :</label>
                        <div class="col-sm-8">
                            <input class="form-control" name="nom_module">

                        </div>
                    </div>
                    <br>

                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Activité :</label>
                        <div class="col-sm-8">
                            <select class="form-select" name="activite">
                                <option value="Cours + TD + TP">Cours + TD + TP</option>
                                <option value="Cours + TD">Cours + TD</option>
                                <option value="Cours + TP">Cours + TP</option>
                                <option value="TD + TP">TD + TP</option>
                                <option value="Cours">Cours</option>
                                <option value="TD">TD</option>


                            </select>

                        </div>
                    </div>
                    <br>

                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">la Spécialité :</label>
                        <div class="col-sm-8">
                            <input class="form-control" name="nom_specialite">

                        </div>
                    </div>
                    <br>
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Chargé de Module :</label>
                        <div class="col-sm-8">
                            <input class="form-control" name="charge_module">

                        </div>
                    </div>
                    <br>
                    <!-- <div class="mb-3">
                        <label class="form-label"></label>
                        <select class="form-select" name="type_lieu">
                            <option value="Amphi">Amphi</option>
                            <option value="Salle">Salle</option>
                        </select>
                    </div> -->

                    <div class="row">
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-primary w-100" name="submit">Ajouter</button>
                        </div>
                        <div class="col-sm-6">
                            <a class="btn btn-outline-primary w-100" href="module.php" role="button">Retourner</a>
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