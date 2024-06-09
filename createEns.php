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
                <h2 class="text-center mb-4">Ajouter un Enseignant</h2>
                <?php
                $error_message = '';

                try {
                    $myproject = new PDO("mysql:host=localhost;dbname=myproject; charset=utf8;", "root", "");
                    $myproject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    $error_message = '<p class="text-danger">Erreur de connexion à la base de données : ' . $e->getMessage() . '</p>';
                }

                if (isset($_POST['submit'])) {
                    $nom = $_POST['nom'];
                    $code = $_POST['code'];
                    $email = $_POST['email'];
                    $grade = $_POST['grade'];

                    try {
                        $stmt = $myproject->prepare("INSERT INTO enseignant (nom, code, email, grade) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$nom, $code, $email, $grade]);
                        echo '<p class="text-success">Données insérées avec succès.</p>';
                    } catch (PDOException $e) {
                        $error_message = '<p class="text-danger">Erreur lors de l\'insertion des données : ' . $e->getMessage() . '</p>';
                    }
                }
                // Vérifie si l'identifiant de l'enseignant à créer est présent dans le formulaire
                if (isset($_POST['submit'])) {
                    try {
                        $myproject = new PDO("mysql:host=localhost;dbname=myproject; charset=utf8;", "root", "");
                        $myproject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        // Récupérez les données soumises du formulaire
                        $nom = $_POST['nom'];
                        $code = $_POST['code'];
                        $email = $_POST['email'];
                        $grade = $_POST['grade'];

                        // Vérifiez si un enseignant avec le même nom existe déjà
                        $stmt_check_existence = $myproject->prepare("SELECT COUNT(*) AS count FROM enseignant WHERE nom = :nom");
                        $stmt_check_existence->bindParam(':nom', $nom);
                        $stmt_check_existence->execute();
                        $result_check_existence = $stmt_check_existence->fetch(PDO::FETCH_ASSOC);

                        if ($result_check_existence['count'] > 0) {
                            echo '<script>alert("Un enseignant avec le même nom existe déjà.");</script>';
                        } else {
                            // Insérez l'enseignant dans la base de données
                            $stmt_insert_enseignant = $myproject->prepare("INSERT INTO enseignant (nom, code, email, grade) VALUES (?, ?, ?, ?)");
                            $stmt_insert_enseignant->execute([$nom, $code, $email, $grade]);
                            echo '<script>alert("Enseignant ajouté avec succès."); window.location.href = "enseignant.php";</script>';
                            exit();
                        }
                    } catch (PDOException $e) {
                        echo '<p class="text-danger">Erreur lors de l\'insertion des données : ' . $e->getMessage() . '</p>';
                    }
                }
                ?>
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Nom et Prenom :</label>
                        <input class="form-control" name="nom" placeholder="Le Nom et Prenom de L'enseignant">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Code :</label>
                        <input class="form-control" name="code" placeholder="Le Code de L'enseignant">
                    </div>



                    <div class="mb-3">
                        <label class="form-label">Email :</label>
                        <input class="form-control" name="email" placeholder="@univ-bejaia.dz">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Grade</label>
                        <select class="form-select" name="grade">
                            <option value="MCA">MCA</option>
                            <option value="MCB">MCB</option>
                            <option value="Pr">Pr</option>
                            <option value="MAA">MAA</option>
                            <option value="MAB">MAB</option>
                            <option value="Doct">Doct</option>
                            <option value="Vacataire">Vacataire</option>
                            <!-- Ajoutez d'autres options si nécessaire -->
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-primary w-100" name="submit">Ajouter</button>
                        </div>
                        <div class="col-sm-6">
                            <a class="btn btn-outline-primary w-100" href="enseignant.php" role="button">Retourner</a>
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