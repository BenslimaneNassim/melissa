<?php
require("session.php");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "myproject";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (isset($_GET['success_message'])) {
    echo '<script>alert("' . $_GET['success_message'] . '")</script>';
}
function getSpecialites($conn) {
    $sql = "SELECT DISTINCT nom_specialite FROM module WHERE id_module IN (
                SELECT id_module FROM examen
            )";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $specialites = [];
    while ($row = $result->fetch_assoc()) {
        $specialites[] = $row['nom_specialite'];
    }
    return $specialites;
}

function getLieuxwithGroupsbyExam($conn, $id_exam) {
    try {
        // Prepare SQL query to fetch Lieux with associated Groups for a given exam
        $query = "
            SELECT 
                lieu.numero,
                CONCAT(groupe.section, groupe.nom_groupe) AS groupe_name
            FROM 
                lieu
            LEFT JOIN 
                lieu_disponibilite ld ON lieu.numero = ld.lieu_numero
            LEFT JOIN 
                groupe_lieu_occuper gl ON ld.id = gl.lieu_id 
            JOIN 
                groupe ON gl.nom_groupe = groupe.nom_groupe
            WHERE 
                ld.examen_id = ? 
                AND gl.section = groupe.section 
                AND gl.nom_specialite = groupe.nom_specialite
            ORDER BY 
                lieu.numero, 
                groupe.section
        ";

        // Prepare and execute the statement
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id_exam); // 'i' indicates integer type
        $stmt->execute();

        // Fetch all rows
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Organize the data into a suitable structure
        $lieux_with_groups = array();
        foreach ($result as $row) {
            $lieu_name = $row['numero'];
            $group_name = $row['groupe_name'];

            // Add group to the corresponding lieu
            if (!isset($lieux_with_groups[$lieu_name])) {
                $lieux_with_groups[$lieu_name] = array(
                    'groups' => array()
                );
            }

            $lieux_with_groups[$lieu_name]['groups'][] = array(
                'nom_groupe' => $group_name
            );
        }

        return $lieux_with_groups;
    } catch (mysqli_sql_exception $e) {
        // Handle database errors gracefully
        return array('error' => 'Database error: ' . $e->getMessage());
    }
}

function getModuleById($conn, $id_module) {
    $sql = "SELECT * FROM module WHERE id_module = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_module);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// function getExamsBySpecialite($conn, $specialite){
//     $sql = "SELECT * FROM examen WHERE id_module IN (
//                 SELECT id_module FROM module WHERE nom_specialite = ?
//             ) ORDER BY date, heureDebut";

//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("s", $specialite);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $exams = [];
//     while ($row = $result->fetch_assoc()) {
//         $exams[] = $row;
//     }
//     return $exams;
// }
function getExamsBySpecialite($conn, $specialite) {
    $sql = "SELECT * FROM examen 
            WHERE id_module IN (
                SELECT id_module FROM module WHERE nom_specialite = ?
            ) ORDER BY periodeDebut, periodeFin, date, heureDebut";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $specialite);
    $stmt->execute();
    $result = $stmt->get_result();
    $exams = [];
    while ($row = $result->fetch_assoc()) {
        $periodeKey = $row['periodeDebut'] . ' - ' . $row['periodeFin'];
        if (!isset($exams[$periodeKey])) {
            $exams[$periodeKey] = [];
        }
        $exams[$periodeKey][] = $row;
    }
    return $exams;
}


function getSurveillants($conn, $exam_id) {
    $sql = "SELECT s.id_examen as id_examen, s.nom_enseignant as nom_enseignant, ld.lieu_numero as lieu_numero  FROM surveillant s, lieu_disponibilite ld WHERE id_examen = ? AND ld.id = s.id_lieu_dispo ORDER BY s.nom_enseignant";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $exam_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $surveillants = [];
    while ($row = $result->fetch_assoc()) {
        $surveillants[] = $row;
    }
    return $surveillants;
}


$specialites = getSpecialites($conn);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

    <style>
        .btn-delete {
			display: inline-block;
			background-color: #dc3545;
			color: white;
			padding: 8px 13px;
			text-align: center;
			text-decoration: none;
			font-size: 17px;
			border: none;
            cursor: pointer;
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


    <!-- My CSS -->
    <link rel="stylesheet" href="assets/css/planning.css">

    <title>Planning</title>
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
            <li>
                <a href="notifAdmin.php">
                    <i class='bx bx-message'></i>
                    <span class="text">Notifications</span>
                </a>
            </li>
        </ul>
        <ul class="side-menu">
            <li>
                <a href="mesplannings.php">
                    <i class='bx bx-calendar'></i>
                    <span class="text">Mes plannings</span>
                </a>
            </li>

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



    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu'></i>
            <a href="#" class="nav-link">Categories</a>
            <form action="#">
                <div class="form-input">
                    <input type="search" placeholder="Search...">
                    <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
                </div>
            </form>
            <input type="checkbox" id="switch-mode" hidden>
            <label for="switch-mode" class="switch-mode"></label>
            <!-- <a href="#" class="notification"> -->
            <!-- <i class='bx bxs-bell'></i> -->
            <!-- <span class="num">8</span> -->
            <!-- </a> -->
            <a href="#" class="profile">
                <i class='bx bx-user'></i>
            </a>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <section id="dash">
                <div class="head-title">
                    <div class="left">
                        <h1>Dashboard</h1>
                        <ul class="breadcrumb">
                            <li>
                                <a href="#">Dashboard</a>
                            </li>
                            <li><i class='bx bx-chevron-right'></i></li>
                            <li>
                                <a class="active" href="#">Mes Planning</a>
                            </li>
                        </ul>
                    </div>

                </div>

                <br>
                <!-- Dashboard content -->
                <!-- <h1>Plannings d'examen</h1> -->

                <?php foreach ($specialites as $specialite): ?>
    <?php
    $examsByPeriod = getExamsBySpecialite($conn, $specialite);
    foreach ($examsByPeriod as $periode => $exams):
    ?>
        <div style="">
            <div class="table-data" style="width:80%; margin: auto; margin-top:20px">
                <div class="order">
                    <div class="head">
                        <i class='bx bx-filter'></i>
                    </div>
                    <div style="float:right">
                        <button onclick="deletePlanning('<?php echo $specialite ?>')" class='btn-delete'><i class='bx bx-trash'></i> Supprimer</button>
                    </div>
                    <h2>Planning pour la spécialité <?php echo $specialite; ?> - Période: <?php echo $periode; ?></h2>
                    <br>
                    <table id="tableauPlanning">
                        <thead>
                            <tr>
                                <th>Date et Heure</th>
                                <th>Module</th>
                                <th>Lieu</th>
                                <th>Surveillants</th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php foreach ($exams as $exam):
                            $module = getModuleById($conn, $exam['id_module']);
                            $lieux_with_groups = getLieuxwithGroupsbyExam($conn, $exam['id']); // Fetch Lieux with associated Groups for the exam
                        ?>
                            <tr>
                                <td><?php echo $exam['date']; ?> <br> <?php echo " de " . $exam['heureDebut']; ?> <br>
                                    <?php echo " à " . $exam['heureFin']; ?> </td>
                                <td><?php echo $module['nom_module']; ?></td>
                                <td>
                                    <?php     $firstLieu = true; ?>
                                    <?php foreach ($lieux_with_groups as $lieu_name => $lieu_data): ?>
                                        <!-- <?php //if (!$firstLieu) echo "<hr>"; ?> -->
                                        <?php $firstLieu = false; ?>
                                        <strong><?php echo $lieu_name; ?>:</strong>
                                        <?php foreach ($lieu_data['groups'] as $group): ?>
                                            <?php echo $group['nom_groupe'] . " "; ?>
                                        <?php endforeach; ?>
                                        <br>
                                    <?php endforeach; ?>
                                </td>
                                <td>
                                    <?php $surveillants = getSurveillants($conn, $exam['id']); ?>
                                    <?php     $firstLieu = true; ?>
                                    <?php foreach ($lieux_with_groups as $lieu_name => $lieu_data): ?>
                                        <?php if (!$firstLieu) echo "<hr>"; ?>
                                        <!-- <strong><?php //echo $lieu_name; ?>:</strong> -->
                                        <?php $firstLieu = false; ?>
                                        <?php $firstSurveillant = true; ?>
                                        <?php foreach ($surveillants as $surveillant): ?>
                                            <?php
                                                if($surveillant['lieu_numero'] == $lieu_name){
                                                    if (!$firstSurveillant) echo ", ";
                                                    $firstSurveillant = false;

                                                    if ($module['charge_module'] == $surveillant['nom_enseignant']) {
                                                        echo "<strong>" . $surveillant['nom_enseignant'] . "</strong>";
                                                    } else {
                                                        echo $surveillant['nom_enseignant'];
                                                    }
                                                }
                                            ?>
                                        <?php endforeach; ?>
                                        <br>
                                    <?php endforeach; ?>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endforeach; ?>
                

            </section>

        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <script>
        function logoutConfirmation() {
            if (confirm("Êtes-vous sûr de vouloir vous déconnecter ?")) {
                return true; // Si l'utilisateur confirme, la déconnexion se produit
            } else {
                return false; // Si l'utilisateur annule, la déconnexion est annulée
            }
        }
    </script>
    <script>
        function deletePlanning(specialite) {
            if (confirm("Êtes-vous sûr de vouloir supprimer ce planning ?")) {
                // Ajoutez la section et le nom de spécialité à l'URL de redirection
                window.location.href = "deleteplanning.php?specialite=" + specialite;
            }
        }
    </script>



    <!-- Bibliothèque jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/datepicker.min.js"></script>

</body>

<script src="assets/js/planning.js"></script>

</html>