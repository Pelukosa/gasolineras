<?php
$servername = "Localhost";
$username = "root";
$password = "";
$dbname = "gas-station";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
