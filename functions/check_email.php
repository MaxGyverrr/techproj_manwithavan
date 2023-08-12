<?php
// Start and handle the $_Session
require_once 'functions/session_protection.php';

$email = $_POST['email'];

$stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($result) > 0) {
    // Email exists in the database
    echo "exists";
} else {
    // Email does not exist in the database
    echo "not_exists";
}
?>