<?php
  session_start();
  require_once 'db_config.php';

  if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php?error=unauthorized");
    exit;
  }

  $user_id = $_SESSION['user_id'];

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db_info = "mysql:host=$db_host;dbname=$db_name;charset=utf8";
    $options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ];
  }

  try {
    $pdo = new PDO($db_info, $db_user, $db_password, $options);
  } catch (PDOException $e) {
    error_log("Database Connection Error for Updating Profile: " . $e->getMessage());
    header("Location: profile.php?update=error&error_msg=db_connection");
    exit;
  }

  try {
    $statement = $pdo->prepare("SELECT first_name, last_name, student_id, phone, email FROM users WHERE id = :id");
    $statement->bindParam(':id', $user_id, PDO::PARAM_STR);
    $statement->execute();
    $user_data = $statement->fetch();
  } catch (PDOException $e) {
    error_log("Fetch Current User Error for Updating Profile: " . $e->getMessage());
    header("Location: profile.php?update=error&error_msg=db_fetch_current_failed");
    exit;
  }

  if(!$user_data) {
    header("Location: profile.php?update=error&error_msg=user_not_found");
    exit;
  }

  $new_first_name = trim($_POST['first_name'] ?? '');
  $new_last_name = trim($_POST['last_name'] ?? '');
  $new_student_id = trim($_POST['student_id'] ?? '');
  $new_phone = trim($_POST['phone'] ?? '');
  $new_email = trim($_POST['email'] ?? '');
  $new_password = $_POST['new_password'] ?? '';
  $confirm_new_password = $_POST['confirm_new_password'] ?? '';

  $sql_field_to_update = [];
  $sql_params = [];

  if (!empty($new_first_name) && $new_first_name !== $user_data['first_name']) {
    if (preg_match('/[0-9]/', $new_first_name)) {
      header("Location: profile.php?update=validation&error_msg=invalid_name");
      exit;
    }
    $sql_field_to_update[] = "first_name = :first_name";
    $sql_params[':first_name'] = $new_first_name;
  }

  if (!empty($new_last_name) && $new_last_name !== $user_data['last_name']) {
    if (preg_match('/[0-9]/', $new_last_name)) {
      header("Location: profile.php?update=validation&error_msg=invalid_name");
      exit;
    }
    $sql_field_to_update[] = "last_name = :last_name";
    $sql_params[':last_name'] = $new_last_name;
  }

  if (!empty($new_student_id) && $new_student_id !== $user_data['student_id']) {
    if (preg_match('/^2022[0-9]{9}$/', $new_student_id)) {
      header("Location: profile.php?update=validation&error_msg=invalid_name");
      exit;
    }
    $sql_field_to_update[] = "student_id = :student_id";
    $sql_params[':student_id'] = $new_student_id;
  }

  if (!empty($new_phone) && $new_phone !== $user_data['phone']) {
    if (preg_match('/^[0-9]{10}$/', $new_phone)) {
      header("Location: profile.php?update=validation&error_msg=invalid_name");
      exit;
    }
    $sql_field_to_update[] = "phone = :phone";
    $sql_params[':phone'] = $new_phone;
  }

  if (empty($new_email)) {
    header("Location: profile.php?update=validation&error_msg=required_email_field");
    exit;
  }
  if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
    header("Location: profile.php?update=validation&error_msg=invalid_email");
    exit;
  }
  if ($new_email !== $user_data['email']) {
    $sql_field_to_update[] = "email = :email";
    $sql_params[':email'] = $new_email;
  }

  if(!empty($new_password)) {
    if ($new_password !== $confirm_new_password) {
      header("Location: profile.php?update=validation&error_msg=password_mismatch");
      exit;
    }
    if (strlen($new_password) < 5) {
      header("Location: profile.php?update=validation&error_msg=password_short");
      exit;
    }
    if(!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?~`]/', $new_password)) {
      header("Location: profile.php?update=validation&error_msg=password_special_char");
      exit;
    }
    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $sql_field_to_update[] = "password = :password";
    $sql_params[':password'] = $new_hashed_password;
  }

  if (count($sql_field_to_update) > 0) {
    $sql = "UPDATE users SET " . implode(', ', $sql_field_to_update) . " WHERE id = :id";
    $sql_params[':id'] = $user_id;

    try {
      $update_statement = $pdo->prepare($sql);
      $update_statement->execute($sql_params);

      header("Location: profile.php?update=success");
      exit;
    } catch (PDOException $e) {
      error_log("Profile Update Error: " . $e->getMessage());
      header("Location: profile.php?update=error&error_msg=db_update_failed");
      exit;
    }
  } else {
    header("Location: profile.php");
    exit;
  }
?>