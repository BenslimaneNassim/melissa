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


function getExamsBySpecialite($conn, $specialite){
    $sql = "SELECT * FROM examen WHERE id_module IN (
                SELECT id_module FROM module WHERE nom_specialite = ?
            ) ORDER BY date, heureDebut";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $specialite);
    $stmt->execute();
    $result = $stmt->get_result();
    $exams = [];
    while ($row = $result->fetch_assoc()) {
        $exams[] = $row;
    }
    return $exams;
}

// function getExamenId($conn, $examen['id']) {
//     $sql = "SELECT * FROM examen WHERE id = ?";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("i", $examen['id']);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $examen = $result->fetch_assoc();
//     return $examen;
// }

function deletePlanning($conn, $specialite) {

    $conn->begin_transaction();
    try {
        $exams = getExamsBySpecialite($conn, $specialite);
        foreach ($exams as $examen) {
            // Step 1: Delete from enseignant_disponibilite
            $sql = "DELETE FROM enseignant_disponibilite WHERE jour = ? AND heureDebut = ? AND heureFin = ? AND nom_enseignant IN (SELECT nom_enseignant FROM surveillant WHERE id_examen = ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $examen['date'], $examen['heureDebut'], $examen['heureFin'], $examen['id']);
            $stmt->execute();
            if ($stmt->errno) {
                throw new Exception("Erreur de suppression enseignant_disponibilite: " . $stmt->error);
            }

            $result = $stmt->get_result();

            // Step 2: Delete from surveillant
            $sql_surveillant = "DELETE FROM surveillant WHERE id_examen = ?";
            $stmt_surveillant = $conn->prepare($sql_surveillant);
            $stmt_surveillant->bind_param("i", $examen['id']);
            $stmt_surveillant->execute();
            if ($stmt->errno) {
                throw new Exception("Erreur de suppression surveillant: " . $stmt->error);
            }

            $stmt_surveillant->close();

        // Step 3: Delete from groupe_lieu_occuper
            $sql_groupe_lieu = "DELETE FROM groupe_lieu_occuper WHERE lieu_id IN (SELECT id FROM lieu_disponibilite WHERE examen_id = ?)";
            $stmt_groupe_lieu = $conn->prepare($sql_groupe_lieu);
            $stmt_groupe_lieu->bind_param("i", $examen['id']);
            $stmt_groupe_lieu->execute();
            if ($stmt_groupe_lieu->errno) {
                throw new Exception("Erreur de suppression groupe_lieu_occuper: " . $stmt->error);
            }

            $stmt_groupe_lieu->close();

            // Step 4: Delete from lieu_disponibilite
            $sql_lieu_dispo = "DELETE FROM lieu_disponibilite WHERE examen_id = ?";
            $stmt_lieu_dispo = $conn->prepare($sql_lieu_dispo);
            $stmt_lieu_dispo->bind_param("i", $examen['id']);
            $stmt_lieu_dispo->execute();
            if ($stmt_lieu_dispo->errno) {
                throw new Exception("Erreur de suppression lieu_disponibilite: " . $stmt->error);
            }

            $stmt_lieu_dispo->close();

            // Step 5: Delete from examen
            $sql_examen = "DELETE FROM examen WHERE id = ?";
            $stmt_examen = $conn->prepare($sql_examen);
            $stmt_examen->bind_param("i", $examen['id']);
            $stmt_examen->execute();
            $stmt_examen->close();

            // Return true if deletion was successful

        }
        $conn->commit();
        return true;
    } catch (Exception $e) {
        // Roll back the transaction if an error occurred
        $conn->rollback();
        echo "Des erreurs se sont produites lors de la suppression des données : " . $e->getMessage();
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['specialite']) && !empty($_GET['specialite'])) {
        $specialite = $_GET['specialite'];
        if (deletePlanning($conn, $specialite)) {
            echo "Suppression réussie";
            $success_message = "Suppression réussie";
            header("Location: mesplannings.php?success_message=$success_message");
            exit();
        
        } else {
            echo "Erreur lors de la suppression";
        }
    }
    else{
        echo "Erreur lors de la suppression";
    }
}else {
    // header("Location: mesplannings.php");
    // exit();
}
?>

