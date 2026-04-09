<?php
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "siseop_db";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Datos del usuario de prueba
$usuario = "JuanPerez";
$email = "test@correo.com";
$password_plana = "123456"; // Esta será la contraseña para loguearte

// Generar el hash seguro
$password_hash = password_hash($password_plana, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (usuario, email, password_hash) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $usuario, $email, $password_hash);

if ($stmt->execute()) {
    echo "Usuario de prueba creado con éxito.";
} else {
    echo "Error: " . $conn->error;
}
?>