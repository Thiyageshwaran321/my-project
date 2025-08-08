<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "user_db"; // make sure this database exists

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form data
$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email'];
$mobilenum = $_POST['mobilenum'];

// Insert into database
$sql = "INSERT INTO users (username, password, email, mobilenum)
        VALUES ('$username', '$password', '$email', '$mobilenum')";

if ($conn->query($sql) === TRUE) {
    echo "New record inserted successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
