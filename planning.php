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

function getEns($conn, $count = 4, $mainTeacher, $feedback)
{
    $teachers = array();
    if ($feedback == 0){
        $sql = "SELECT nom FROM enseignant WHERE nom != ? AND grade != 'Doct' AND grade != 'Pr' ORDER BY RAND() LIMIT ?";
    }else{
        $sql = "SELECT nom FROM enseignant WHERE nom != ? ORDER BY RAND() LIMIT ?";
    }
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $mainTeacher, $count);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $teachers[] = $row['nom'];
    }

    return $teachers;
}
// function selectRandomLocation($conn)
// {
//     $lieu = array();
//     $sql = "SELECT numero, type_lieu, capacite FROM lieu WHERE type_lieu = 'amphi' ORDER BY RAND() LIMIT 1";
//     $result = $conn->query($sql);

//     if ($result->num_rows > 0) {
//         while ($row = $result->fetch_assoc()) {
//             $lieu = $row;
//         }
//     } else {
//         $sql = "SELECT numero, type_lieu, capacite FROM lieu ORDER BY RAND() LIMIT 1";
//         $result = $conn->query($sql);
//         if ($result->num_rows > 0) {
//             while ($row = $result->fetch_assoc()) {
//                 $lieu = $row;
//             }
//         }
//     }

//     return $lieu;
// }
function selectRandomLocation($conn)
{
    $lieu = array();
    $sql = "SELECT numero, type_lieu, capacite FROM lieu ORDER BY RAND(), capacite desc LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $lieu = $row;
        }
    }

    return $lieu;
}

function getGroupes($conn, $section, $nom_specialite)
{
    $sql = "SELECT id_groupe, capacite FROM groupe WHERE section = ? AND nom_specialite = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $section, $nom_specialite);
    $stmt->execute();
    $result = $stmt->get_result();
    $groupes = [];
    while ($row = $result->fetch_assoc()) {
        $groupes[] = $row;
    }
    return $groupes;
}

function getLieuxDisponibles($conn)
{
    $sql = "SELECT numero, capacite FROM lieu";
    $result = $conn->query($sql);
    $lieux = [];
    while ($row = $result->fetch_assoc()) {
        $lieux[] = $row;
    }
    return $lieux;
}

function choisirLieu($lieux)
{
    return $lieux[array_rand($lieux)];
}

function capaciteSuffisante($groupe, $lieu)
{
    return $groupe['nombre_etudiants'] <= $lieu['capacite'];
}

function affecterGroupesAuxLieux($conn, $section, $nom_specialite)
{
    $groupes = getGroupes($conn, $section, $nom_specialite);
    $lieux = getLieuxDisponibles($conn);

    foreach ($groupes as $groupe) {
        $lieu = choisirLieu($lieux);
        if (capaciteSuffisante($groupe, $lieu)) {
            $sql = "UPDATE groupe SET lieu_attribue = ? WHERE id_groupe = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $lieu['numero'], $groupe['id_groupe']);
            $stmt->execute();
        } else {
            echo "La capacité du lieu n'est pas suffisante pour le groupe ", $groupe['id_groupe'], "\n";
        }
    }
}

function getAvailableHours($conn)
{
    $hours = array();
    $sql = "SELECT debut, fin FROM Horaire";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $hours[] = $row;
        }
    }
    return $hours;
}
function getHourbyId($conn, $id)
{
    $sql = "SELECT debut, fin FROM Horaire WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $horaire = $result->fetch_assoc();
    return $horaire;
}

function getGroupesForSpecialite($conn, $specialite)
{
    $sql = "SELECT nom_groupe, section, nombre_etudiants FROM groupe WHERE nom_specialite = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $specialite);
    $stmt->execute();
    $result = $stmt->get_result();
    $groupes = array();
    while ($row = $result->fetch_assoc()) {
        $groupes[] = $row;
    }
    return $groupes;
}

function isLieuAvailable($conn, $date, $heureDebut, $heureFin, $lieu_numero)
{
    $sql = "SELECT COUNT(*) AS count FROM lieu_disponibilite WHERE lieu_numero = ? AND jour = ? AND ((heureDebut >= ? AND heureDebut < ?) OR (heureFin > ? AND heureFin <= ?))";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $lieu_numero, $date, $heureDebut, $heureFin, $heureDebut, $heureFin);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    return $count == 0;
}

function isEnseignantAvailable($conn, $date, $heureDebut, $heureFin, $nom_enseignant)
{
    $sql = "SELECT COUNT(*) AS count FROM enseignant_disponibilite WHERE nom_enseignant = ? AND jour = ? AND heureDebut = ? AND heureFin = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nom_enseignant, $date, $heureDebut, $heureFin);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    return $count == 0;
}







function getSectionsForSpecialite($conn, $specialite)
{
    $sql = "SELECT DISTINCT section FROM groupe WHERE nom_specialite = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $specialite);
    $stmt->execute();
    $result = $stmt->get_result();
    $sections = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sections[] = $row;
        }
    }

    return $sections;
}


function getGroupesForSection($conn, $specialite, $section)
{
    $query = "SELECT * FROM groupe WHERE nom_specialite = ? AND section = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $specialite, $section);
    $stmt->execute();
    $result = $stmt->get_result();
    $groupes = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $groupes;
}
// Nassim's edit
function selectRandomLocationCapacite($conn, $capacite_min)
{
    $query = "SELECT * FROM lieu WHERE capacite >= ? ORDER BY RAND() LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $capacite_min);
    $stmt->execute();
    $result = $stmt->get_result();
    $lieu = $result->fetch_assoc();
    $stmt->close();
    return $lieu;
}

// Fonction pour obtenir la capacité nécessaire pour tous les groupes d'une section
function getCapaciteNecessaireSection($conn, $section, $specialite)
{
    $sql = "SELECT SUM(nombre_etudiants) AS total FROM groupe WHERE section = ? and nom_specialite = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $section, $specialite);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'];
}

$daysOfWeek = array(
    'Sunday' => 'Dimanche',
    'Monday' => 'Lundi',
    'Tuesday' => 'Mardi',
    'Wednesday' => 'Mercredi',
    'Thursday' => 'Jeudi',
);

// Fonction pour vérifier si un module est déjà planifié pour une journée donnée
function isModuleScheduledForDay($planning, $date)
{
    foreach ($planning as $examen) {
        if ($examen['date'] === $date) {
            return true; // Un module est déjà planifié pour cette journée
        }
    }
    return false;
}

// function calculer 
function getWorkingDays($startDate, $endDate) {
    $begin = strtotime($startDate);
    $end   = strtotime($endDate);
    $no_days  = 0;

    while ($begin <= $end) {
        $what_day = date("N",$begin);
        if($what_day < 5 || $what_day == 7)  // 5 and 6 are weekend, 7 is Sunday
            $no_days++;
        $begin += 86400; // +1 day
    };

    return $no_days;
}
function findFirstExamDateLaxity($no_days, $no_exams){
    $margin = $no_days - (2*$no_exams);
    return $margin;
}
// Fonction pour générer aléatoirement un planning initial pour une spécialité spécifique et une période spécifique
function generateRandomPlanning($conn, $specialite, $dateDebut, $dateFin, $feedback)
{
    $planning = array();
    echo $specialite;
    // Préparer la requête pour sélectionner les modules affectés à la spécialité spécifique
    $sql = "SELECT id_module, nom_module, activite, nom_specialite, charge_module FROM module WHERE nom_specialite = ? ORDER BY RAND()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $specialite);
    $stmt->execute();
    $result = $stmt->get_result();

    // Récupérer les horaires disponibles
    // $availableHours = getAvailableHours($conn);

    $selectedMorningSlot = rand(1, 3);
    // Un des trois crénaux du matin est sélectionné aléatoirement
    $useMorningSlot = (bool)rand(0, 1);
    // Commence le matin ou aprés midi

    echo ' HI';
    echo $result->num_rows;
    // Générer aléatoirement un planning pour chaque module dans la période spécifiée
    if ($result->num_rows > 0) {

        $numRows = $result->num_rows;
        $working_days = getWorkingDays($dateDebut, $dateFin);
        if ($numRows > $working_days){
            echo $working_days;
            $error_message = "test";
            echo $error_message;
            return $error_message;
        }else{

            $first_margin = findFirstExamDateLaxity($working_days, $numRows);
            $iteration = $first_margin;

            while ($row = $result->fetch_assoc()) {

                $mainTeacher = $row['charge_module'];
                // Générer une heure pour l'examen
                $selectedSlot = $useMorningSlot ? $selectedMorningSlot : $selectedMorningSlot + 3;
                $heure_examen = getHourbyId($conn, $selectedSlot);
                $heureDebut = $heure_examen['debut'];
                $heureFin = $heure_examen['fin'];
                //alterner pour le prochain examen
                $useMorningSlot = !$useMorningSlot;
                // $startmicrotime = microtime(true);
                $check_if_stuck = 0;
                do {
                    $check_if_stuck = $check_if_stuck + 1;
                    if ($feedback != 0){
                        $margin = rand(0, $first_margin);
                    } else{
                        $margin = $first_margin-$iteration;
                    }
                    // $date = date('Y-m-d', strtotime($dateDebut)+$first_margin-$iteration);
                    $date = date('Y-m-d', strtotime("+$margin days", strtotime($dateDebut)));
                    // $date = date('Y-m-d', strtotime("-$iteration days", strtotime($date)));

                    if ($iteration > 0) {
                        $iteration--;
                    }
                    // echo $date;
                    $dayOfWeek = date('N', strtotime($date)); // 1 (for Monday) through 7 (for Sunday)
                    if($dayOfWeek == 5){
                        $date = date('Y-m-d', strtotime($date . ' + 2 day'));
                    }elseif($dayOfWeek == 6){
                        $date = date('Y-m-d', strtotime($date . ' + 1 day'));
                    }
                    // Checker si l'algorithme est bloqué ici pour recommencer la regéneration
                    // $end = microtime(true);
                    // $executionTime = $end - $startmicrotime;
                    echo $check_if_stuck;
                    if ($check_if_stuck >10){
                        return false;
                    }
                } while ($dayOfWeek == 5 || $dayOfWeek == 6 || isModuleScheduledForDay($planning, $date) || !isEnseignantAvailable($conn, $date, $heureDebut, $heureFin, $mainTeacher)); // Re-generate date if it's Friday (5) or Saturday (6)
                if($iteration != 0){
                    $iteration++;
                }
                $first_margin = $iteration;
                $dateDebut = date('Y-m-d', strtotime($date . ' + 2 day'));

                // Sélection aléatoire des enseignants disponibles
                $enseignants = getEns($conn, rand(3, 4), $mainTeacher, $feedback); // Sélectionne aléatoirement entre 4 et 5 enseignants

                // Vérifier la disponibilité de chaque enseignant et le remplacer s'il n'est pas disponible
                $enseignantsDisponibles = array();
                $enseignantsDisponibles[] = $mainTeacher;
    
                foreach ($enseignants as $enseignant) {
                    if (isEnseignantAvailable($conn, $date, $heureDebut, $heureFin, $enseignant)) {
                        $enseignantsDisponibles[] = $enseignant;
                    } else {
                        // Sélectionner un autre enseignant disponible
                        $nouvelEnseignant = findAvailableTeacher($conn, $date, $heureDebut, $heureFin);
                        if ($nouvelEnseignant) {
                            $enseignantsDisponibles[] = $nouvelEnseignant;
                        }
                    }
                }
                $enseignants = $enseignantsDisponibles;
                // Sélection aléatoire d'un lieu disponible
                // Récupérer les groupes pour cette spécialité
                $groupes = getGroupesForSpecialite($conn, $specialite);


                // Récupérer les sections pour cette spécialité
                $sections = getSectionsForSpecialite($conn, $specialite);

                // Affecter un lieu à chaque section et à ses groupes, en vérifiant la disponibilité des lieux
                $sectionsWithLieux = array();
                // $firstLieu = null; // Lieu pour les trois premiers groupes
                // $secondLieu = null; // Lieu pour les autres groupes
                $lieuxWithSectionGroupe = array();

                $newGroupes = array();
                $lieux_non_dispos_locaux = array();

                foreach ($sections as $section) {
                    $sectionsWithLieux[] = array(
                        "section" => $section['section']
                        // "numero_lieu" => $currentLieu['numero'],
                        // "type_lieu" => $currentLieu['type_lieu']
                    );

                    $sectionGroupes = array_filter($groupes, function($groupe) use ($section) {
                        return $groupe['section'] == $section['section'];
                    });

                    $currentLieu = null;
                    $currentCapacity = 0;

                    foreach ($sectionGroupes as &$groupe) {
                        if ($currentLieu === null || $currentCapacity < $groupe['nombre_etudiants']) {
                            // Select a new venue if no venue has been assigned yet or if the current venue's capacity is insufficient
                            $startmicrotime = microtime(true);  
                            do {
                                if ($feedback == 0){
                                    $currentLieu = selectRandomLocationCapacite($conn, getCapaciteNecessaireSection($conn, $groupe['section'], $specialite));
                                }else{
                                    $currentLieu = selectRandomLocationCapacite($conn, $groupe['nombre_etudiants']);
                                }
                                $end = microtime(true);
                                if (($end - $startmicrotime) > 4){
                                    echo 'second one';
                                    return false;
                                }
                            } while (!isLieuAvailable($conn, $date, $heureDebut, $heureFin, $currentLieu['numero']) || in_array($currentLieu['numero'], $lieux_non_dispos_locaux));
                            $lieux_non_dispos_locaux[] = $currentLieu['numero'];
                            
                            $currentCapacity = $currentLieu['capacite'];
                            // $sectionsWithLieux[] = array(
                            //     "section" => $section['section'],
                            //     "numero_lieu" => $currentLieu['numero'],
                            //     "type_lieu" => $currentLieu['type_lieu']
                            // );
                        }

                        // Assign the group to the current venue
                        $groupe['numero_lieu'] = $currentLieu['numero'];
                        $groupe['type_lieu'] = $currentLieu['type_lieu'];
                        $currentCapacity -= $groupe['nombre_etudiants'];
                        $newGroupes[] = $groupe;
                        // Add Lieu with concatenation of SectionGroupe
                    }
                }
                unset($groupe); // Libérer la référence

                




                // Ajouter l'examen au planning avec les enseignants sélectionnés et les groupes
                $planning[] = array(
                    "id_module" => $row['id_module'],
                    "nom_module" => $row['nom_module'],
                    "activite" => $row['activite'],
                    "nom_specialite" => $row['nom_specialite'],
                    "date" => $date,
                    "heureDebut" => $heureDebut,
                    "heureFin" => $heureFin,
                    "enseignants" => $enseignants,
                    "lieux" => $lieux_non_dispos_locaux,
                    // "numero_lieu" => $lieu['numero'],
                    // "type_lieu" => $lieu['type_lieu'],
                    "charge_module" => $mainTeacher,
                    "groupes" => $newGroupes,
                    "sections" => $sectionsWithLieux
                );
            }
        }
    }
    //Print first exam teacher
    return $planning;
    //
}




// 

// Fonction pour filtrer le planning en fonction des contraintes
function filterPlanning($planning, $conn)
{
    $filteredPlanning = array();


    foreach ($planning as $examen) {
        // Récupérer les informations de l'examen
        $date = $examen['date'];
        $heureDebut = $examen['heureDebut'];
        $heureFin = $examen['heureFin'];
        $enseignants = $examen['enseignants'];
        $groupes = $examen['groupes'];
        $sections = $examen['sections'];




        $enseignantsDisponibles = array();
        foreach ($enseignants as $enseignant) {
            if (isEnseignantAvailable($conn, $date, $heureDebut, $heureFin, $enseignant)) {
                $enseignantsDisponibles[] = $enseignant;
            } else {
                // Sélectionner un autre enseignant disponible
                $nouvelEnseignant = findAvailableTeacher($conn, $date, $heureDebut, $heureFin);
                if ($nouvelEnseignant) {
                    $enseignantsDisponibles[] = $nouvelEnseignant;
                }
            }
        }

        // Si pas tous les enseignants ont pu être remplacés, ignorer cet examen
        if (count($enseignantsDisponibles) < count($enseignants)) {
            continue;
        }

        // Mettre à jour les enseignants de l'examen avec les enseignants disponibles
        $examen['enseignants'] = $enseignantsDisponibles;






        // Toutes les contraintes sont satisfaites, ajouter l'examen filtré au planning filtré
        $filteredPlanning[] = $examen;
    }

    return $filteredPlanning;
}


// Fonction pour trouver un enseignant disponible à une date et heure donnée
function findAvailableTeacher($conn, $date, $heureDebut, $heureFin)
{
    $sql = "SELECT nom FROM enseignant WHERE nom NOT IN (
                SELECT nom_enseignant FROM Enseignant_Disponibilite 
                WHERE jour = ? AND 
                ((heureDebut <= ? AND heureFin >= ?) OR (heureDebut <= ? AND heureFin >= ?))
            )";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $date, $heureDebut, $heureDebut, $heureFin, $heureFin);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['nom'];
    } else {
        return null;
    }
}

// Fonction pour sélectionner les lieux disponibles pour une date et une période donnée
function selectAvailableLocations($conn, $date, $heureDebut, $heureFin)
{
    $sql = "SELECT numero, type_lieu, capacite FROM lieu WHERE numero NOT IN (
                SELECT lieu_numero FROM lieu_disponibilite
                WHERE jour = ? AND
                ((heureDebut <= ? AND heureFin >= ?) OR (heureDebut <= ? AND heureFin >= ?))
            )";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $date, $heureDebut, $heureDebut, $heureFin, $heureFin);
    $stmt->execute();
    $result = $stmt->get_result();

    $locations = array();
    while ($row = $result->fetch_assoc()) {
        $locations[] = $row;
    }

    return $locations;
}



// Traitement du formulaire si soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Si le formulaire est soumis et des examens sont générés
    if (isset($_POST['enregistrer'])) {
        if (isset($_POST['planningFiltre'])) {
            
            
            // Decode the JSON string back to an array
            $planningFiltre = json_decode($_POST['planningFiltre'], true);
            $dateDebut = json_decode($_POST['dateDebut'], true);
            $dateFin = json_decode($_POST['dateFin'], true);
            $specialite = json_decode($_POST['specialitee'], true);
            // echo json_encode($planningFiltre);
        
            foreach ($planningFiltre as $examen) {
                // Convertir les heures au format TIME
                $heureDebut = date('H:i:s', strtotime($examen['heureDebut']));
                $heureFin = date('H:i:s', strtotime($examen['heureFin']));

                // Stocker les enseignants dans une variable
                $enseignants = implode(",", $examen['enseignants']);
                $lieux = implode(",", $examen['lieux']);
                // Commencer une transaction pour garder les données cohérentes
                $conn->begin_transaction();

                $error = false; // Initialize error flag

                try {
                    // Insert data into the Examen table
                    $sql = "INSERT INTO Examen (date, heureDebut, heureFin, id_module) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    if (!$stmt) {
                        throw new Exception("Erreur de préparation de la requête : " . $conn->error);
                    }
                    echo $examen['id_module'];
                    $stmt->bind_param("sssi", $examen['date'], $heureDebut, $heureFin, $examen['id_module']); //, $lieu_numero
                    $stmt->execute();
                    if ($stmt->errno) {
                        throw new Exception("Erreur d'insertion dans la base de données: " . $stmt->error);
                    }
                    $examen_id = $stmt->insert_id;
                    echo $examen_id;

                    

                    // Insert data into the lieu_disponibilite table
                    foreach ($examen['lieux'] as $lieu_numero) {
                        $sql = "INSERT INTO lieu_disponibilite (lieu_numero, jour, heureDebut, heureFin, examen_id) VALUES (?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ssssi", $lieu_numero, $examen['date'], $heureDebut, $heureFin, $examen_id);
                        $stmt->execute();
                        if ($stmt->errno) {
                            throw new Exception("Erreur d'insertion dans la table lieu_disponibilite: " . $stmt->error);
                        }
                    
                        // Check if the insertion was successful before inserting into groupe_lieu_occuper
                        $last_insert_id = $stmt->insert_id;
                        if ($last_insert_id) {
                            foreach ($examen['groupes'] as $group) {
                                if ($group['numero_lieu'] === $lieu_numero) {
                                    $nom_groupe = $group['nom_groupe'];
                                    $section = $group['section']; // Add section attribute
                                    $nom_specialite = $examen['nom_specialite']; // Add nom_specialite attribute
                                    $insert_sql = "INSERT INTO groupe_lieu_occuper (nom_groupe, section, nom_specialite, lieu_id) VALUES (?, ?, ?, ?)";
                                    $insert_stmt = $conn->prepare($insert_sql);
                    
                                    // Retrieve the id of the corresponding lieu_disponibilite entry
                                    $id_query = "SELECT id FROM lieu_disponibilite WHERE lieu_numero = ? AND jour = ? AND heureDebut = ?";
                                    $id_stmt = $conn->prepare($id_query);
                                    $id_stmt->bind_param("sss", $lieu_numero, $examen['date'], $examen['heureDebut']); // Assuming $heureDebut is a variable holding the start time                                    $id_stmt->execute();
                                    $id_stmt->execute();
                                    $id_result = $id_stmt->get_result()->fetch_assoc();
                    
                                    if ($id_result) {
                                        $lieu_id = $id_result['id'];
                                        $insert_stmt->bind_param("sssi", $nom_groupe, $section, $nom_specialite, $last_insert_id);
                                        $insert_stmt->execute();
                                        if ($insert_stmt->errno) {
                                            throw new Exception("Erreur d'insertion dans la table groupe_lieu_occuper: " . $insert_stmt->error);
                                        }
                                    } else {
                                        // Handle the case when the lieu_numero doesn't exist in lieu_disponibilite
                                        echo "lieu_numero $lieu_numero does not exist in lieu_disponibilite. Skipping insertion into groupe_lieu_occuper.";
                                    }
                                }
                            }
                        } else {
                            echo "Error: Unable to retrieve the last inserted id.";
                        }
                    }
                    
                    // Insert data into the enseignant_disponibilite table
                    foreach ($examen['enseignants'] as $enseignant) {
                        $sql = "INSERT INTO enseignant_disponibilite (nom_enseignant, jour, heureDebut, heureFin) VALUES (?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ssss", $enseignant, $examen['date'], $heureDebut, $heureFin);
                        $stmt->execute();
                        if ($stmt->errno) {
                            throw new Exception("Erreur d'insertion dans la table Enseignant_Examen: " . $stmt->error);
                        }

                        // Insert into surveillant table
                        $sql_surveillant = "INSERT INTO surveillant (id_examen, nom_enseignant) VALUES (?, ?)";
                        $stmt_surveillant = $conn->prepare($sql_surveillant);
                        $stmt_surveillant->bind_param("ss", $examen_id, $enseignant);
                        $stmt_surveillant->execute();
                        if ($stmt_surveillant->errno) {
                            throw new Exception("Erreur d'insertion dans la table surveillant: " . $stmt_surveillant->error);
                        }
                    }

                    // Commit the transaction
                    $conn->commit();

                    // echo "Les données ont été ajoutées avec succès à toutes les tables.";
                } catch (Exception $e) {
                    // Roll back the transaction if an error occurred
                    $conn->rollback();
                    echo "Des erreurs se sont produites lors de l'ajout des données : " . $e->getMessage();
                }

            }
        }
    }else{



    // Récupération de la spécialité sélectionnée
    $specialite = $_POST["specialite"];

    // Récupération de la période de planification des examens
    $periode = $_POST["periode"];
    list($dateDebut, $dateFin) = explode(" à ", $periode);



    // Générer un planning initial pour la spécialité "Informatique" pour une période donnée (1er au 30 mai 2024)
    // $planningInitial = generateRandomPlanning($conn, $specialite, $dateDebut, $dateFin);

    // // Filtrer le planning initial en fonction des contraintes
    // $planningFiltre = filterPlanning($planningInitial, $conn);

    $executionTime = null;
    // $planningInitial = array();
    $planningFiltre = array();
    $tentative = 0;
    do {
        // $startmicrotime = microtime(true);

        // Generate initial planning
        $planningInitial = generateRandomPlanning($conn, $specialite, $dateDebut, $dateFin, $tentative);
        // echo json_encode($planningInitial);
        if ($planningInitial == false){
            $tentative++;
            echo "tentative : " .$tentative;
        }
        // Filter the initial planning

        // echo json_encode($planningFiltre);

        // $end = microtime(true);
        // $executionTime = $end - $startmicrotime;
    } while (($tentative < 5) && ($planningInitial == false));
    if ( !empty($planningInitial) && $planningInitial != false ){
        $planningFiltre = filterPlanning($planningInitial, $conn);
    }else{
        echo json_encode($planningInitial);
        echo "<script>alert('Géneration de planning echouée')</script>";
    }

    }
}

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

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-+dMXx3zS3DzSsJ0DWqs2Pl6UmMwLU9Uks37VPc6vFN9pQ9zC/25d8PzmWh/+wemel2wPCic8k2K10bUzcaIdhA==" crossorigin="anonymous" />

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }

        form {
            margin-top: 20px;
            text-align: center;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .generate-btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .generate-btn:hover {
            background-color: #0056b3;
        }

        .download-btn {
            background-color: #4caf50;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .download-btn:hover {
            background-color: #388e3c;
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
                                <a class="active" href="#">Planning</a>
                            </li>
                        </ul>
                    </div>

                </div>

                <br>
                <!-- Dashboard content -->
                <h1>Génération du planning d'examens</h1>
                <?php if (isset($planningFiltre)) : ?>
                    <button class="download-btn" onclick="downloadExcel()">Télécharger Planning Excel</button>
                    <button class="download-btn" id="downloadBtn">Télécharger PDF</button>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div style="margin: auto; display:flex; width: 80%; justify-content: space-around">
                        <div>
                            <label for="specialite">Spécialité :</label>
                            <select name="specialite" id="specialite">
                                <?php
                                // Connexion à la base de données
                                $pdo = new PDO('mysql:host=localhost;dbname=myproject', 'root', '');
                                $stmt = $pdo->query('SELECT nom_specialite FROM specialite WHERE nom_specialite NOT IN (
                                    SELECT nom_specialite from module where id_module IN (
                                        SELECT id_module from examen
                                    )
                                )');
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='" . $row['nom_specialite'] . "'>" . $row['nom_specialite'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label for="periode">Période des examens :</label>
                            <input type="text" id="periode" name="periode" placeholder="YYYY-MM-DD à YYYY-MM-DD"><br><br>
                        </div>

                    </div>
                    <button type="submit" class="generate-btn" name="submit"><i class="fas fa-cog"></i> Générer le planning</button>
                    <?php if (isset($planningFiltre)) : ?>
                        <input type="hidden" name="specialitee" value='<?php echo json_encode($specialite); ?>' />
                        <input type="hidden" name="dateDebut" value='<?php echo json_encode($dateDebut); ?>' />
                        <input type="hidden" name="dateFin" value='<?php echo json_encode($dateFin); ?>' />
                        <input type="hidden" name="planningFiltre" value='<?php echo json_encode($planningFiltre); ?>' />
                        <input class="download-btn" type="submit" value="Enregistrer" name="enregistrer">
                    <?php endif; ?>



                </form>
                <div class="table-data">
                    <div class="order">
                        <div class="head">
                            <h3>Generation de planning</h3>

                            <i class='bx bx-filter'></i>
                        </div>


                        <?php

                        ?>
                        <!-- Affichage du planning d'examens généré -->
                        <?php if (isset($planningFiltre) && !empty($planningFiltre)) : ?>
                            <h2>Planning pour la spécialité <?php echo $specialite; ?> dans la période du
                                <?php echo $dateDebut; ?> au <?php echo $dateFin; ?>
                            </h2>
                            <br>
                            <table id="tableauPlanning">
                                <tr>
                                    <th>Date et Heure</th>
                                    <th>Module</th>
                                    <th>Lieu</th>
                                    <th>Surveillants</th>
                                </tr>
                                <?php foreach ($planningFiltre as $exam) : ?>
                                    <tr>
                                        <td><?php echo $exam['date']; ?> <br> <?php echo " de " . $exam['heureDebut']; ?> <br>
                                            <?php echo " à " . $exam['heureFin']; ?> </td>
                                        <td><?php echo $exam['nom_module']; ?></td>
                                        <td>
                                            <?php
                                            // // Récupérer les sections et groupes
                                            foreach($exam['lieux'] as $lieu){
                                                echo "<strong> " . $lieu . " : </strong>";
                                                foreach ($exam['sections'] as $section) {
                                                    foreach ($exam['groupes'] as $groupe) {
                                                        if (($groupe['section'] == $section['section']) && ($lieu == $groupe['numero_lieu'] ) ) {
                                                            echo $section['section'] . $groupe['nom_groupe'] . " ";
                                                        }
                                                    }
                                                }
                                                echo "<br>";
                                            }
                                            ?>
                                        </td>
                                        <!-- Afficher le type de lieu et le numéro -->
                                        <td>
                                            <?php foreach ($exam['enseignants'] as $enseignant) : ?>
                                                <?php if ($enseignant == $exam['charge_module']) : ?>
                                                    <strong><?php echo $enseignant; ?></strong><br>
                                                <?php else : ?>
                                                    <?php echo $enseignant; ?><br>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

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
        function downloadExcel() {
            // Récupérer le tableau
            var table = document.querySelector('table');

            // Convertir le tableau en format Excel
            var wb = XLSX.utils.table_to_book(table);

            // Générer le nom du fichier
            var fileName = 'planning_examens.xlsx';

            // Convertir le workbook en binaire Excel
            var binaryData = XLSX.write(wb, {
                bookType: 'xlsx',
                type: 'binary'
            });

            // Créer un objet Blob pour le contenu binaire
            var blob = new Blob([s2ab(binaryData)], {
                type: 'application/octet-stream'
            });

            // Créer un objet URL à partir du Blob
            var url = URL.createObjectURL(blob);

            // Créer un lien temporaire et déclencher le téléchargement
            var a = document.createElement('a');
            a.href = url;
            a.download = fileName;
            a.click();

            // Libérer l'URL
            URL.revokeObjectURL(url);
        }

        // Fonction pour convertir la chaîne en tableau de bytes
        function s2ab(s) {
            var buf = new ArrayBuffer(s.length);
            var view = new Uint8Array(buf);
            for (var i = 0; i != s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
            return buf;
        }
    </script>

    <script>
        // JavaScript pour télécharger le PDF
        document.getElementById("downloadBtn").addEventListener("click", function() {
            var table = document.getElementById("tableauPlanning");
            var html = table.outerHTML;

            // Convertit le HTML en PDF
            var pdf = new jsPDF();
            pdf.fromHTML(html, 15, 15);

            // Télécharge le PDF
            pdf.save("table.pdf");
        });
    </script>

    <!-- Bibliothèque jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/datepicker.min.js"></script>

</body>

<script src="assets/js/planning.js"></script>
</body>

</html>