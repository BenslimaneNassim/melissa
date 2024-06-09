<?php
include 'connection.php';
session_start();



try {
    $myproject = new PDO("mysql:host=localhost;dbname=myproject; charset=utf8;", "root", "");
    $myproject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $error_message = '<p class="text-danger">Erreur de connexion à la base de données : ' . $e->getMessage() . '</p>';
}

if (isset($_SESSION['nom'])) {

    $nom = $_SESSION['nom'];

    $query = "SELECT n.notification_name, n.message, n.created_at
    FROM notification n
    INNER JOIN notification_enseignant ne ON n.n_id = ne.notification_id
    WHERE ne.nom_enseignant = ? ORDER BY created_at DESC";
    $stmt = $myproject->prepare($query);
    $stmt->execute([$nom]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérez le nombre de notifications destinées à l'enseignant connecté
    $query_count = "SELECT COUNT(*) as count_notifications
          FROM notification n
          INNER JOIN notification_enseignant ne ON n.n_id = ne.notification_id
          WHERE ne.nom_enseignant = ?";
    $stmt_count = $myproject->prepare($query_count);
    $stmt_count->execute([$nom]);
    $row_count = $stmt_count->fetch(PDO::FETCH_ASSOC);
    $count_notifications = $row_count['count_notifications'];



?>


    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="assets/css/dispo.css">
        <title>Planning</title>
        <style>
            .badge {
                position: relative;
                top: -12px;
                right: -0.3px;
                width: 20px;
                height: 20px;
                background-color: red;
                color: white;
                border-radius: 50%;
                padding: 5px 8px;
                font-size: 12px;
                font-weight: bold;
                z-index: 1;

            }

            .notification-list {
                max-height: 256px;
                /* ou la hauteur souhaitée */
                overflow-y: auto;
                padding: 0;
                margin: 0;
                list-style-type: none;
            }

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

            .msg-menu {
                width: 450px;
                position: absolute;
                top: 60px;
                right: 100px;
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

            @media screen and (max-width: 768px) {
                .container {
                    flex-direction: column;
                }
            }

            .profile-menu ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .msg-menu ul {
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

            .msg-menu ul li {
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

            .msg-menu ul li i {
                margin-right: 20px;
                /* Espace entre l'icône et le texte */
            }



            .profile-menu ul li:hover {
                background-color: #f0f0f0;
                cursor: pointer;
            }

            .msg-menu ul li:hover {
                background-color: #f0f0f0;
                cursor: pointer;
            }


            .profile-menu.active {
                display: block;
                max-height: 300px;
                padding: 10px 0;
                /* Ajustement du padding */
            }

            .msg-menu.active {
                display: block;
                max-height: 300px;
                padding: 10px 0;
                /* Ajustement du padding */
            }




            /* .container {
			display: flex;
			justify-content: space-between;
		} */

            @media screen and (max-width: 576px) {
                .container {
                    flex-direction: column;
                }
            }

            @media screen and (max-width: 1085px) {
                .container {
                    display: flex;
                    flex-direction: column;
                }
            }
        </style>
    </head>

    <body>

        <!-- Sidebar -->
        <div class="sidebar">
            <a href="#" class="logo">
                <img src="Logo.png" alt="logo">
                <div class="logo-name"><span>Exam</span>Plan</div>
            </a>
            <ul class="side-menu">
                <li><a href="dispo.php"><i class='bx bxs-calendar'></i>Disponibilités</a></li>
                <li class="active"><a href="planningEns.php"><i class="bx bxs-calendar-event"></i>
                        Planning</a></li>
                <li><a href="profilEns.php"><i class='bx bx-cog'></i>Paramétres</a></li>
            </ul>
            <ul class="side-menu">
                <li>
                    <a href="login.php" class="logout" onclick="return logoutConfirmation()">
                        <i class='bx bx-log-out-circle'></i>
                        Logout
                    </a>
                </li>
            </ul>
        </div>
        <!-- End of Sidebar -->

        <!-- Main Content -->
        <div class="content">
            <!-- Navbar -->
            <nav>


                <form action="#">
                    <div class="form-input">
                        <button class="search-btn" type="submit" hidden><i class='bx bx-search'></i></button>
                    </div>
                </form>
                <input type="checkbox" id="theme-toggle" hidden>
                <label for="theme-toggle" class="theme-toggle"></label>
                <a href="#" class=notif onclick="toggleNotif()"><span class="material-symbols-outlined">
                        notifications
                    </span>
                    <?php if ($count_notifications > 0) { ?>
                        <div class="badge" id="bell-count">
                            <span><?php echo $count_notifications; ?></span>
                        </div>
                    <?php } ?>
                </a>
                <div class="msg-menu" id="notifMsg">
                    <ul class="notification-list" data-tab-for="notification" data-page="notifications">
                        <?php
                        foreach ($notifications as $notification) : ?>
                            <li><strong><?php echo htmlspecialchars($notification['notification_name']); ?></strong>: <?php echo htmlspecialchars($notification['message']); ?>
                                <br>
                                <small>Envoyé le : <?php echo date('d/m/Y H:i', strtotime($notification['created_at'])); ?></small>
                            </li>

                        <?php endforeach;  ?>
                    </ul>

                </div>

                <a href="#" class="profile" onclick="toggleProfileMenu()">
                    <span class="material-symbols-outlined">
                        account_circle
                    </span> </a>
                <div class="profile-menu" id="profileMenu">
                    <ul>
                        <li>
                            <?php echo "$nom"; ?>
                        </li>
                        <li>
                            <i class='bx bx-user'></i>
                            <a href="profilEns.php"> Mon Profil</a>
                        </li>
                        <li>
                            <i class='bx bx-edit'></i>
                            <a href="profilEns.php"> Modifer Profile</a>
                        </li>
                        <li>
                            <i class='bx bx-log-out'></i>
                            <a href="login.php" onclick="logoutConfirmation()"> Deconnexion</a>
                        </li>
                    </ul>
                </div>
            </nav>
            <!-- End of Navbar -->

            <main>





                <div class="bottom-data">
                    <div class="orders">
                        <div class="header">

                            <h3>Planning</h3>
                            <a href="#"><button class="btn btn-secondary">
                                    Telecharger </button></a>
                        </div>

                    </div>
                </div>

            </main>

        </div>

        </main>

        </div>
        <script>
            //LIGHT & DARK MODE//
            const toggler = document.getElementById('theme-toggle');
            toggler.addEventListener('change', function() {
                if (this.checked) {
                    document.body.classList.add('dark');
                } else {
                    document.body.classList.remove('dark');
                }
            });
            //DROPDOWNMENU//
            function toggleProfileMenu() {
                var profileMenu = document.getElementById('profileMenu');
                profileMenu.classList.toggle('active');
            }
            //DROPDOWN NOTIFICATION///
            function toggleNotif() {
                var notifMsg = document.getElementById('notifMsg');
                notifMsg.classList.toggle('active');

            }
            //LOGPUT CONFIRMATION//
            function logoutConfirmation() {
                if (confirm("Êtes-vous sûr de vouloir vous déconnecter ?")) {
                    return true; // le user confirme et se deconnecte
                } else {
                    return false; // le user refuse
                }
            }
        </script>

        <script src="assets/js/dispo.js"></script>
    </body>

    </html>
<?php } else {
    header("location:login.php?error");
    exit();
} ?>