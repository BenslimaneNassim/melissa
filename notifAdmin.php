<?php
include('connection.php');

try {
    $myproject = new PDO("mysql:host=localhost;dbname=myproject;charset=utf8", "root", "");
    $myproject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo ('<p style="color: green;">Connexion à la base de données réussie.</p>');
} catch (PDOException $e) {
    die('<p style="color: red;">Erreur de connexion à la base de données : ' . $e->getMessage() . '</p>');
}

// Préparation de la requête SQL
$sql = "SELECT * FROM enseignant";

try {
    // Exécution de la requête SQL
    $stmt = $myproject->query($sql);
} catch (PDOException $e) {
    die('<p style="color: red;">Erreur lors de l\'exécution de la requête : ' . $e->getMessage() . '</p>');
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/notif.css">
    <title>Enseignants</title>
    <style>
        .profile-menu {
            position: absolute;
            top: 60px;
            right: 10px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: none;
            z-index: 9999;
            padding: 10px 0;
            max-height: 0;
            overflow: hidden;
            margin-right: 5px;
            transition: max-height 0.3s ease, padding 0.3s ease;
        }

        /* @media screen and (max-width: 768px) {
			.container {
				flex-direction: column;
			}
		}
	 */
        .profile-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .profile-menu ul li {
            padding: 10px 20px;
            display: flex;
            align-items: center;
            /* Centrer verticalement */
            margin-right: 10px;
        }

        .profile-menu ul li i {
            margin-right: 20px;
            /* Espace entre l'icône et le texte */
        }

        .profile-menu ul li:hover {
            background-color: #f0f0f0;
            cursor: pointer;
        }

        .profile-menu.active {
            display: block;
            max-height: 300px;
            padding: 10px 0;
            /* Ajustement du padding */
        }

        .container {
            display: flex;
            justify-content: space-between;
        }

        /* @media screen and (max-width: 576px) {
			.container {
				flex-direction: column;
			}
		} */
        @media screen and (max-width: 1085px) {
            .container {
                /* display: flex; */
                flex-direction: column;
            }
        }

        .btn-create {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn-create:hover {
            background-color: #45a049;
        }

        .btn-create i {
            margin-right: 5px;
        }

        .btn-edit {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            border-radius: 3px;
            transition: background-color 0.3s;
        }

        .btn-edit:hover {
            background-color: #0056b3;
        }

        .btn-edit i {
            margin-right: 5px;
        }

        .btn-delete {
            display: inline-block;
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            border: none;
            /* Ajoutez cette ligne pour supprimer les bordures */
            border-radius: 3px;
            transition: background-color 0.3s;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        .btn-delete i {
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <!-- SIDEBAR -->
    <section id="sidebar" class="hide">
        <a href="#" class="brand">
            <i class='bx bx-grid-alt'></i>
            <span class="text">Admin</span>
        </a>
        <ul class="side-menu top">
            <li>
                <a href="dashboard.php">
                    <!-- <i class='bx bxs-dashboard' ></i> -->
                    <i class='bx bx-stats'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="enseignant.php">
                    <i class='bx bx-user'></i>
                    <span class="text">Enseignants</span>
                </a>
            </li>
            <li>
                <a href="etudiant.php">
                    <i class='bx bxs-group'></i>
                    <span class="text">Etudiants</span>
                </a>
            </li>
            <li>
                <a href="salle.php">
                    <i class='bx bxs-school'></i>
                    <span class="text">Salles</span>
                </a>
            </li>
            <li>
                <a href="module.php">
                    <i class='bx bx-file'></i>
                    <span class="text">Modules</span>
                </a>
            </li>
            <li class="active">
                <a href="notifAdmin.php">
                    <i class='bx bx-message'></i>
                    <span class="text">Notifications</span>
                </a>
            </li>
        </ul>
        <ul class="side-menu">
            <li>
                <a href="planning.php">
                    <i class='bx bxs-cog'></i>
                    <span class="text">Generation Planning</span>
                </a>
            </li>
            <li>
                <a href="session.php?logout=true" class="logout" onclick="return logoutConfirmation()">
                    <i class='bx bxs-log-out-circle'></i>
                    <span class="text">Déconnexion</span>
                </a>
            </li>
        </ul>
    </section>
    <!-- SIDEBAR -->

    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu'></i>
            <a href="#" class="nav-link">Categories</a>
            <form action="recherche_enseignant.php" method="POST">
                <div class="form-input">
                    <!-- <input type="search" name="search" placeholder="Search..."> -->
                    <input type="search" name="search" id="searchInput" placeholder="Search...">
                    <button type="button" class="search-btn" id="searchButton"><i class='bx bx-search'></i></button>


                </div>
            </form>
            <input type="checkbox" id="switch-mode" hidden>
            <label for="switch-mode" class="switch-mode"></label>
            <a href="#" class="notification">
                <i class='bx bxs-bell'></i>
                <!-- <span class="num">8</span> -->
            </a>
            <a href="#" class="profile" onclick="toggleProfileMenu()">
                <i class='bx bx-user'></i>
            </a>
            <div class="profile-menu" id="profileMenu">
                <ul>
                    <li>
                        <i class='bx bx-home'></i>
                        <a href="dashboard.php"> Dashboard</a>
                    </li>
                    <li>
                        <i class='bx bx-user'></i>
                        <a href="profil.php"> Mon Profil</a>
                    </li>
                    <li>
                        <i class='bx bx-edit'></i>
                        <a href="profil.php"> Edit Profile</a>
                    </li>
                    <li>
                        <i class='bx bx-log-out'></i>
                        <a href="#" onclick="logoutConfirmation()"> Logout</a>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>

            <section id="dash">
                <div class="head-title">
                    <div class="left">
                        <h1>Les Notifications</h1>
                        <ul class="breadcrumb">
                            <li>
                                <a href="#">Dashboard</a>
                            </li>
                            <li><i class='bx bx-chevron-right'></i></li>
                            <li>
                                <a class="active" href="#">Notification</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Message de succès -->
                <?php if (isset($_GET['success']) && $_GET['success'] == 1) : ?>
                    <div class="alert alert-success" role="alert">
                        Notification envoyée avec succès !
                    </div>
                <?php endif; ?>

                <div class="table-data">
                    <div class="order">
                        <div class="head">
                            <div class="col-md-6 ">
                                <div class="form-container">
                                    <h3>Notification</h3>
                                    <form action="sendNotif.php" method="POST">
                                        <div class="mb-3">
                                            <label for="nom" class="col-sm-4 col-form-label">Enseignant:</label>
                                            <select multiple class="form-select" name="ensg[]" aria-label="Default select example">
                                                <?php
                                                $query = "SELECT nom FROM enseignant";
                                                $result = mysqli_query($con, $query);
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo "<option value='" . $row['nom'] . "'>" . $row['nom'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <?php
                                        include 'connexion.php';
                                        // $query = "SELECT nom FROM enseignant";
                                        // $result = mysqli_query($con, $query);
                                        // while ($row = mysqli_fetch_assoc($result)) {
                                        //     echo "<div class='form-check'>";
                                        //     echo "<input class='form-check-input' type='checkbox' name='ensg[]' value='" . $row['nom'] . "'>";
                                        //     echo "<label class='form-check-label'>" . $row['nom'] . "</label>";
                                        //     echo "</div>";
                                        // }
                                        ?>
                                        </select>
                                        <div class="mb-3">
                                            <label for="notification_name" class="form-label">Titre</label>
                                            <input type="text" class="form-control" id="notification_name" name="notification_name" aria-required="true" autofill="off">
                                        </div>
                                        <div class="mb-3">
                                            <label for="message" class="form-label">Message</label>
                                            <textarea class="form-control" id="message" name="message" rows="3"></textarea>
                                        </div>



                                        <button type="submit" class="btn btn-success">Envoyer</button>
                                    </form>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </section>


        </main>
    </section>

    <script>
        function toggleProfileMenu() {
            var profileMenu = document.getElementById('profileMenu');
            profileMenu.classList.toggle('active');
        }

        function logoutConfirmation() {
            if (confirm("Êtes-vous sûr de vouloir vous déconnecter ?")) {
                return true; // Si l'utilisateur confirme, la déconnexion se produit
            } else {
                return false; // Si l'utilisateur annule, la déconnexion est annulée
            }
        }
    </script>


    <script src="assets/js/enseignant.js"></script>


</body>

</html>