<?php
$servername = "Localhost";
$username = "arfix311_arfix31";
$password = "";
$dbname = "arfix311_arfix";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
