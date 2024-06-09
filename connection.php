<?php

$con = mysqli_connect("localhost", "root", "", "myproject");
if ($con->connect_error) {
    die("cONNECTION failed" . $con->connect_error);
}
