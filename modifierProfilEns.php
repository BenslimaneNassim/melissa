<?php
include 'connection.php';
session_start();

if (isset($_SESSION['nom'])) {
    // Récupération du nom de l'utilisateur
    $nom = $_SESSION['nom'];
    // Requête SQL pour sélectionner les informations de l'utilisateur connecté
    $sql = "SELECT * FROM enseignant WHERE nom = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $nom);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Récupération des informations de l'utilisateur
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        echo "<p>Aucune information trouvée pour cet enseignant.</p>";
    }

    // Si le formulaire a été soumis
    if (isset($_POST['submit'])) {
        $email = $_POST['email'];
        $code = $_POST['code'];
        $grade = $_POST['grade'];

        // Requête de mise à jour
        $sqlUpdate = "UPDATE enseignant SET email = ?, code = ?, grade = ? WHERE nom = ?";
        $stmtUpdate = mysqli_prepare($con, $sqlUpdate);
        mysqli_stmt_bind_param($stmtUpdate, "ssss", $email, $code, $grade, $nom);

        if (mysqli_stmt_execute($stmtUpdate)) {
            header('Location:modifierProfilEns.php?msg=success');
            exit();  // Assurez-vous d'arrêter l'exécution après la redirection
        } else {
            die(mysqli_error($con));
        }
    }

?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
        <title>Modifier Mon Profil</title>
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

            .arrow {
                color: black;
            }
        </style>
    </head>

    <body>
        <div class="container d-flex justify-content-center align-items-center vh-100">
            <div class=" col-md-6">
                <div class="form-container">
                    <a href="profilEns.php" class="arrow"><span class="material-symbols-outlined">
                            arrow_back
                        </span></a>

                    <!-- Vérifiez si le message de succès doit être affiché -->
                    <?php
                    if (isset($_GET['msg']) && $_GET['msg'] === 'success') {
                        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                                Modification réussie
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
                    }
                    ?>

                    <h5 class="text-center">Modifier votre profil</h5>

                    <!-- Formulaire de modification -->
                    <form method="post" action="">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" value="<?php echo isset($_SESSION['nom']) ? $_SESSION['nom'] : ''; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="code" class="form-label">Code</label>
                            <input type="text" class="form-control" id="code" name="code" value="<?php echo htmlspecialchars($row['code']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Grade</label>
                            <select class="form-select" name="grade">
                                <option value="MAA" <?php if ($row['grade'] == 'MAA') echo 'selected'; ?>>MAA</option>
                                <option value="MCA" <?php if ($row['grade'] == 'MCA') echo 'selected'; ?>>MCA</option>
                                <option value="MCB" <?php if ($row['grade'] == 'MCB') echo 'selected'; ?>>MCB</option>
                                <option value="Pr" <?php if ($row['grade'] == 'Pr') echo 'selected'; ?>>Pr</option>
                                <option value="Vacataire" <?php if ($row['grade'] == 'Vacataire') echo 'selected'; ?>>Vacataire</option>
                            </select>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    </body>

    </html>

<?php
} else {
    header("Location: login.php?error");
    exit();
}
?>