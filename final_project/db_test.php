<?php
// Simple DB connection tester for local XAMPP setup
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'pkbuilders';

echo "<h2>DB connection test</h2>\n";

if (!function_exists('mysqli_connect')) {
    echo "<p><strong>mysqli extension is not available.</strong> Check your PHP installation and enable the mysqli extension in php.ini.</p>";
    exit();
}

// Try to connect (suppress warnings with @ so we can show a controlled message)
$conn = @new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo "<p><strong>Connection failed:</strong> (" . htmlspecialchars($conn->connect_errno) . ") " . htmlspecialchars($conn->connect_error) . "</p>";
    echo "<p>Check XAMPP MySQL is running and credentials/database name are correct.</p>";
} else {
    echo "<p style='color:green;'><strong>Connected successfully</strong> to database '<code>" . htmlspecialchars($dbname) . "</code>' as user '<code>" . htmlspecialchars($username) . "</code>'.</p>";
    $conn->close();
}

echo "<hr><p>Useful next steps:<ul>\n<li>Open XAMPP Control Panel and ensure MySQL is started.</li>\n<li>If connection fails, check `c:\\xampp\\mysql\\data` for DB files, or recreate the database via phpMyAdmin.</li>\n</ul></p>";

?>
