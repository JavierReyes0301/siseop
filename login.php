<?php
// login.php: Procesa el formulario de inicio de sesión de forma segura.
session_start();

// --- Configuración de la base de datos ---
$servername = "localhost";
$username_db = "root"; 
$password_db = "";     
$dbname = "siseop_db"; 
// NOTA: El puerto 3306 es el predeterminado. No necesitamos especificarlo explícitamente.

// Conectar a la base de datos
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Verificar la conexión a la BD inmediatamente
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Verificar si la petición es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Usamos 'email' aquí porque es el nombre del campo en tu formulario HTML (asumimos)
    $email_input = htmlspecialchars($_POST['email']);
    $password_input = htmlspecialchars($_POST['password']);

    // --- CORRECCIÓN CLAVE ---
    // Usar consultas preparadas: Seleccionamos 'id', 'password_hash' y 'usuario'
    // Los nombres coinciden exactamente con tu tabla.
    $stmt = $conn->prepare("SELECT id, password_hash, usuario FROM usuarios WHERE email = ?");
    
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    
    $stmt->bind_param("s", $email_input); // "s" significa que el parámetro es string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verificar la contraseña con la función segura de PHP
        if (password_verify($password_input, $row['password_hash'])) {
            // ÉXITO: Inicia sesión y REDIRECCIONA

            // Asignar variables de sesión usando el nombre de columna 'usuario'
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['usuario']; // Corregido de 'nombre' a 'usuario'
            $_SESSION['loggedin'] = true; 

            // Redireccionar al dashboard 
            header("Location: dashboard.php"); 
            exit(); 
        } else {
            // ERROR: Contraseña incorrecta
            die("Error: Contraseña incorrecta.");
        }
    } else {
        // ERROR: Correo electrónico no registrado
        die("Error: Correo electrónico no registrado.");
    }
} else {
    // Si alguien intenta acceder a login.php directamente sin un POST
    header("Location: index.html");
    exit();
}
?>