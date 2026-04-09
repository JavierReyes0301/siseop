<?php
// login.php: Procesa el formulario de inicio de sesión con Supabase (PostgreSQL)
session_start();

// --- Configuración de Supabase (Extraído de tu cadena de conexión) ---
$host = "db.maopuzbvxucsarrydmte.supabase.co";
$port = "5432";
$dbname = "postgres";
$user = "postgres";
$password = "J19941203030127r"; // Reemplaza [YOUR-PASSWORD] con la real

try {
    // Conexión usando PDO (necesario para PostgreSQL/Supabase)
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $conn = new PDO($dsn, $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a Supabase: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Limpiamos el email, pero NO la contraseña (para no alterar caracteres especiales)
    $email_input = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password_input = $_POST['password'];

    try {
        // Consulta preparada con PDO
        $stmt = $conn->prepare("SELECT id, password_hash, usuario FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email_input]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Verificar contraseña
            if (password_verify($password_input, $row['password_hash'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['usuario'];
                $_SESSION['loggedin'] = true; 

                header("Location: dashboard.php"); 
                exit(); 
            } else {
                die("Error: Contraseña incorrecta.");
            }
        } else {
            die("Error: Correo electrónico no registrado.");
        }
    } catch (PDOException $e) {
        die("Error en la consulta: " . $e->getMessage());
    }
} else {
    header("Location: index.html");
    exit();
}
?>