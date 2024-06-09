<?php
include 'connection.php';

session_start();

try {
    $myproject = new PDO("mysql:host=localhost;dbname=myproject;charset=utf8", "root", "");
    $myproject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('<p class="text-danger">Erreur de connexion à la base de données : ' . $e->getMessage() . '</p>');
}

if (isset($_POST['ensg']) && isset($_POST['notification_name']) && isset($_POST['message'])) {
    $ensg = $_POST['ensg'];
    $notification_name = $_POST['notification_name'];
    $message = $_POST['message'];
    foreach ($ensg as $ens) {
        // Insérer la notification
        $insert_notification = "INSERT INTO notification (notification_name, message, active) VALUES (?, ?, 1)";
        $stmt = $myproject->prepare($insert_notification);
        $stmt->execute([$notification_name, $message]);
        $notification_id = $myproject->lastInsertId();

        // Lier la notification à chaque enseignant sélectionné
        $link_notification_prof = "INSERT INTO notification_enseignant (notification_id, nom_enseignant) VALUES (?, ?)";
        $stmt = $myproject->prepare($link_notification_prof);
        $stmt->execute([$notification_id, $ens]);
    }

    header("Location: notifAdmin.php?success=1");
    //     exit();
} else {
    echo "Please fill in all fields.";
}
