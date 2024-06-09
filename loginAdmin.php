<?php
session_start();
include 'connection.php';

if (isset($_POST['name']) && isset($_POST['password'])) {
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    $name = validate($_POST['name']);
    $password = validate($_POST['password']);

    if (empty($name)) {
        header("Location:adminLogin.php?error=name is requiered");
    } else if (empty($password)) {
        header("Location:adminLogin.php?error=Password is requiered");
    } else {
        $sql = "select * from admin where name='$name' and password='$password'";
        $result = mysqli_query($con, $sql);
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            if ($row['name'] === $name && $row['password'] === $password) {
                $_SESSION['name'] = $row['name'];
                $_SESSION['password'] = $row['[password]'];

                #   $_SESSION['nom'] = $row['nom'];
                # $_SESSION['enseignant_id'] = $row['enseignant_id'];


                #  $_SESSION['id'] = $row['id'];
                header("location:dashboard.php");
                exit();

                # code...
            }
        } else {
            header("location:loginAdmin.php?error=Incorrect Code or Password");
            exit();
        }
    }
} else {
    header("location:loginForm.php?error");
    exit();
}
