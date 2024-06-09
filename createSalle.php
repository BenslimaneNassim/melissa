<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Enseignant</title>
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
                <h2 class="text-center mb-4">Ajouter un Lieu</h2>
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
                        $numero = $_POST['numero'];
                        $capacite = $_POST['capacite'];
                        $type_lieu = $_POST['type_lieu'];

                        // Vérifiez si une salle avec le même numéro existe déjà
                        $stmt_check_existence = $myproject->prepare("SELECT COUNT(*) AS count FROM lieu WHERE numero = :numero");
                        $stmt_check_existence->bindParam(':numero', $numero);
                        $stmt_check_existence->execute();
                        $result_check_existence = $stmt_check_existence->fetch(PDO::FETCH_ASSOC);

                        if ($result_check_existence['count'] > 0) {
                            echo '<script>alert("Une salle avec le même numéro existe déjà.");</script>';
                        } else {
                            // Insérez la salle dans la base de données
                            $stmt_insert_salle = $myproject->prepare("INSERT INTO lieu (numero, capacite, type_lieu) VALUES (?, ?, ?)");
                            $stmt_insert_salle->execute([$numero, $capacite, $type_lieu]);
                            echo '<script>alert("Salle ajoutée avec succès."); window.location.href = "salle.php";</script>';
                            exit();
                        }
                    } catch (PDOException $e) {
                        echo '<p class="text-danger">Erreur lors de l\'insertion des données : ' . $e->getMessage() . '</p>';
                    }
                }
                ?>
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Numero :</label>
                        <input class="form-control" name="numero" placeholder="Le numero de la salle">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Capacité :</label>
                        <input class="form-control" name="capacite" placeholder="La capacite de la salle">
                    </div>


                    <!-- <div class="mb-3">
                        <label class="form-label">Type de lieu :</label>
                        <input class="form-control" name="type_lieu" placeholder="Le Type de la salle">
                    </div> -->
                    <div class="mb-3">
                        <label class="form-label">Type de lieu :</label>
                        <select class="form-select" name="type_lieu">
                            <option value="Amphi">Amphi</option>
                            <option value="Salle">Bloc</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-primary w-100" name="submit">Ajouter</button>
                        </div>
                        <div class="col-sm-6">
                            <a class="btn btn-outline-primary w-100" href="salle.php" role="button">Retourner</a>
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