<?php
session_start();

require_once 'db_config.php';

$db_info = "mysql:host=$db_host;dbname=$db_name;charset=utf8";
$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
];

try {
  $pdo = new PDO($dsn, $db_user, $db_password, $options);
} catch (PDOException $e) {
  error_log("Database Connection Error: " . $e->getMessage());
  header("Location: login.html?error=3");
  exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = isset($_POST['username']) ? trim($_POST['username']) : '';
  $password = isset($_POST['password']) ? $_POST['username'] : '';

  if (empty($username) || empty($password)) {
    header("Location: login.html?error=2");
    exit;
  }

  try {
    $statement = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :username");
    $statement->bindParam(':username', $username, PDO::PARAM_STR);
    $statement->execute();
    $user = $statement->fetch();

    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['login'] = true;
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];
      session_regenerate_id(true);

      header("Location: index.php");
      exit;
    } else {
      header("Location: login.html?error=1");
      exit;
    }
  } catch (PDOException $e) {
    error_log("Login Query Error: " . $e->getMessage());
    header("Location: login.html?error=3");
    exit;
  }
} else {
  header("Location: login.html");
  exit;
}
?>