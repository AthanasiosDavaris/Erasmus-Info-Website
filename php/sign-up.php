<?php
session_start();
require_once 'db_config.php';

$errors = [];

$first_name = trim($_POST["firstName"] ?? '');
$last_name = trim($_POST["lastName"] ?? '');
$student_id = trim($_POST["studentId"] ?? '');
$phone = trim($_POST["phone"] ?? '');
$email = trim($_POST["email"] ?? '');
$username = trim($_POST["username"] ?? '');
$password = $_POST["password"] ?? '';
$confirm_password = $_POST["confirmPassword"] ?? '';
$terms = isset($_POST["terms"]);

if (empty($first_name)) {
  $errors['first_name'] = "Το όνομα είναι υποχρεωτικό.";
} elseif (preg_match('/[0-9]/', $first_name)) {
  $errors['first_name'] = "Το όνομα δεν πρέπει να περιέχει ψηφία.";
}

if (empty($last_name)) {
  $errors['last_name'] = "Το επώνυμο είναι υποχρεωτικό.";
} elseif (preg_match('/[0-9]/', $last_name)) {
  $errors['last_name'] = "Το επώνυμο δεν πρέπει να περιέχει ψηφία.";
}

if (empty($student_id)) {
  $errors['student_id'] = "Ο αριθμός μητρώου είναι υποχρεωτικός.";
} elseif (!preg_match('/^2022[0-9]{9}$/', $student_id)) {
  echo $student_id;
  $errors['student_id'] = "Ο αριθμός μητρώου πρέπει να αποτελείται από 13 ψηφία, να ξεκινάει με '2022' και να περιέχει μόνο ψηφία.";
}

if (empty($phone)) {
  $errors['phone'] = "Το τηλέφωνο είναι υποχρεωτικό.";
} elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
  echo $phone;
  $errors['phone'] = "Το τηλέφωνο πρέπει να αποτελείται από ακριβώς 10 ψηφία.";
}

if (empty($email)) {
  $errors['email'] = "Η διεύθυνση mail είναι υποχρεωτική.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $errors['email'] = "Η διεύθυνση mail δεν είναι έγκυρη.";
}

if (empty($username)) {
  $errors['username'] = "Το όνομα χρήστη είναι υποχρεωτικό.";
}

if (empty($password)) {
  $errors['password'] = "Ο κωδικός πρόσβασης είναι υποχρεωτικός.";
} elseif (strlen($password) < 5) {
  $errors['password'] = "Ο κωδικός πρόσβασης πρέπει να έχει τουλάχιστον 5 χαρακτήρες.";
} elseif (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?~`]/', $password)) {
    $errors['password'] = "Ο κωδικός πρόσβασης πρέπει να περιέχει τουλάχιστον έναν ειδικό χαρακτήρα.";
}

if (empty($confirm_password)) {
  $errors['confirm_password'] = "Η επιβεβαίωση κωδικού είναι υποχρεωτική.";
} elseif ($password !== $confirm_password) {
  $errors['confirm_password'] = "Οι κωδικοί πρόσβασης δεν ταιριάζουν.";
}

if(!$terms) {
  $errors['terms'] = "Πρέπει να αποδεχτείτε τους Όρους Χρήσης και την Πολιτική Απορρήτου.";
}

if (empty($errors)) {
  $con = mysqli_connect($db_host, $db_user, $db_password, $db_name);

  if(!$con) {
    $errors['database'] = "Πρόβλημα σύνδεσης με τη βάση δεδομένων. Παρακαλώ προσπαθήστε αργότερα.";
  } else {
    mysqli_set_charset($con, "utf8");

    $prepared_username_token = mysqli_prepare($con, "SELECT id FROM users WHERE username = ?");
    mysqli_stmt_bind_param($prepared_username_token, "s", $username);
    mysqli_stmt_execute($prepared_username_token);
    mysqli_stmt_store_result($prepared_username_token);

    if (mysqli_stmt_num_rows($prepared_username_token) > 0) {
      $errors['username'] = "Αυτό το όνομα χρήστη χρησιμοποιείται ήδη. Παρακαλώ επιλέξτε ένα άλλο.";
    }
    mysqli_stmt_close($prepared_username_token);

    if (empty($errors)) {
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);

      $insert_user_token = mysqli_prepare($con, "INSERT INTO users (first_name, last_name, student_id, phone, email, username, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
      mysqli_stmt_bind_param($insert_user_token, "sssssss", $first_name, $last_name, $student_id, $phone, $email, $username, $hashed_password);

      if (mysqli_stmt_execute($insert_user_token)) {
        echo "Εγγραφή επιτυχής! Μπορείτε τώρα να <a href='login.html'>συνδεθείτε</a>.";
        mysqli_stmt_close($insert_user_token);
        mysqli_close($con);
        exit();
      } else {
        $errors['database'] = "Σφάλμα κατά την εγγραφή. Παρακαλώ προσπαθήστε αργότερα.";
      }
      mysqli_stmt_close($insert_user_token);
    }
    mysqli_close($con);
  }
}

if (!empty($errors)) {
  echo "<h2>Η εγγραφή απέτυχε:</h2><ul>";
  foreach ($errors as $key => $message) {
    echo "<li>" . htmlspecialchars($message) . "</li>";
  }
  echo "</ul>";
  echo "<p><a href='sign-up.html'>Επιστροφή στη φόρμα εγγραφής</a></p>";
}

?>