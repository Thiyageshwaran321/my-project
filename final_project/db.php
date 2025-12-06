<?php
$conn = mysqli_connect("localhost", "root", "", "pkbuilders");

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}
?>
