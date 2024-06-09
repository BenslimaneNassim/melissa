<?php
try {
    $myproject = new PDO("mysql:host=localhost;dbname=myproject; charset=utf8;", "root", "");
    //echo ('<p style="color: green;">connexion a la base de donnes faites avec succès</p>');
} catch (\Throwable $th) {
    die('<p style="color: red;">connexion a la base de donnes faites avec succes</p>' . $th->getMessage());
}
?>
<!-- base : myproject table : administrateur -->