<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "user_db"; // your database name

// 2. Connection
$conn = new mysqli($servername, $username, $password, $database);

// 3. Connection Check
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 4. Check if POST data is set
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 5. SQL Query
    $sql = "INSERT INTO login_users (username, password) VALUES ('$username', '$password')";

    // 6. Run Query
    if ($conn->query($sql) === TRUE) {
        echo "Data inserted successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "No data received!";
}

// 7. Close Connection
$conn->close();
?>
