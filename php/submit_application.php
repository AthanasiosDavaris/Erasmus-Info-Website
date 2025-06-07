<?php
  session_start();
  require_once 'db_config.php';

  if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=unauthorized');
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: application.php?error=invalid_access');
    exit;
  }

  $user_id = $_SESSION['user_id'];
  $errors = [];

  $percentage_passed_lessons = $_POST['percentage_passed_lessons'] ?? null;
  $gpa = $_POST['gpa'] ?? null;
  $english_level = $_POST['english_level'] ?? null;
  $other_languages = $_POST['other_languages'] ?? null;
  $university1 = $_POST['university1'] ?? null;
  $university2 = $_POST['university2'] ?? '';
  $university3 = $_POST['university3'] ?? '';
  $terms = isset($_POST['terms']);

  if (empty($percentage_passed_lessons) || !is_numeric($percentage_passed_lessons)) {
    $errors[] = "Το ποσοστό περασμένων μαθημάτων είναι υποχρεωτικό.";
  }
  if (empty($gpa) || !is_numeric($gpa)) {
    $errors[] = "Ο μέσος όρος είναι υποχρεωτικός.";
  }
  if (empty($english_level)) {
    $errors[] = "Το επίπεδο Αγγλικών είναι υποχρεωτικό.";
  }
  if (empty($other_languages)) {
    $errors[] = "Η επιλογή για γνώση άλλων γλωσσών είναι υποχρεωτική.";
  }
  if (empty($university1)) {
    $errors[] = "Η 1η επιλογή Πανεπιστημίου είναι υποχρεωτική.";
  }
  if (!$terms) {
    $errors[] = "Πρέπει να αποδεχτείτε τους όρους συμμετοχής.";
  }

  $upload_dir = '../media/files/upload/';
  
  if (!isset($_FILES['grades_file']) || $_FILES['grades_file']['error'] !== UPLOAD_ERR_OK) {
    $errors[] = "Το αρχείο της αναλυτικής βαθμολογίας είναι υποχρεωτική.";
  }
  if (!isset($_FILES['english_certificate_file']) || $_FILES['english_certificate_file']['error'] !== UPLOAD_ERR_OK) {
    $errors[] = "Το πτυχίο Αγγλικών είναι υποχρεωτικό.";
  }

  if(!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    header('Location: application.php');
    exit;
  }

  function createUniqueFilename($file, $user_id, $prefix) {
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    return $prefix . '_' . $user_id . '_' . time() . '.' . $extension;
  }

  $grades_filename = createUniqueFilename($_FILES['grades_file'], $user_id, 'grades');
  $grades_filepath = $upload_dir . $grades_filename;
  if(!move_uploaded_file($_FILES['grades_file']['temp_name'], $grades_filepath)) {
    $errors[] = "Σφάλμα κατά την αποθήκευση της αναλυτικής βαθμολογίας.";
  }

  $english_certificate_filename = createUniqueFilename($_FILES['english_certificate_file'], $user_id, 'english_certificate');
  $english_certificate_filepath = $upload_dir . $english_certificate_filename;
  if(!move_uploaded_file($_FILES['english_certificate_file']['temp_name'], $english_certificate_filepath)) {
    $errors[] = "Σφάλμα κατά την αποθήκευση του πτυχίου Αγγλικών.";
  }


  if ($other_languages === 'yes' && empty($_FILES['other_languages_certificates_files']['name'][0])) {
    $errors[] = "Επιλέξατε 'Ναι' για άλλες γλώσσες, αλλά δεν ανεβάσατε κάποιο πτυχίο.";
  } else {
    $other_languages_certificates_paths = [];
    if (isset($_FILES['other_languages_certificates_files']) && !empty(array_filter($_FILES['other_languages_certificates_files']['name']))) {
      foreach ($_FILES['other_languages_certificates_files']['temp_name'] as $key => $temp_name) {
        if ($_FILES['other_languages_certificates_files']['error'][$key] === UPLOAD_ERR_OK) {
          $filename = 'other_language_' . $user_id . '_' . time() . '_' . $key . '.pdf';
          $filepath = $upload_dir . $filename;
          if (move_uploaded_file($temp_name, $filepath)) {
            $other_languages_certificates_paths[] = $filepath;
          }
        }
      }
    }
    $other_languages_certificates_filepaths = !empty($other_languages_certificates_paths) ? json_encode($other_languages_certificates_paths) : null;
  }
  

  if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors; // check again
    header('Location: application.php');
    exit;
  }

  try {
    $db_info = "mysql:host=$db_host;dbname=$db_name;charset=utf8";
    $pdo = new PDO($db_info, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $user_statement = $pdo->prepare("SELECT first_name, last_name, student_id FROM users WHERE id = ?");
    $user_statement->execute([$user_id]);
    $user_info = $user_statement->fetch();

    if(!$user_info) {
      throw new Exception("Δεν ήταν δυνατή η εύρεση πληροφοριών χρήστη για την ολοκλήρωση της αίτησης.");
    }

    $sql = "INSERT INTO applications (user_id, first_name, last_name, student_id, percentage_passed_lessons, gpa, english_level, other_languages, university1, university2, university3, grades_filepath, english_certificate_filepath, other_languages_certificates_filepaths) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $statement = $pdo->prepare($sql);
    $statement->execute([
      $user_id,
      $user_info['first_name'],
      $user_info['last_name'],
      $user_info['student_id'],
      $percentage_passed_lessons,
      $gpa,
      $english_level,
      $other_languages,
      $university1,
      $university2,
      $university3,
      $grades_filepath,
      $english_certificate_filepath,
      $other_languages_certificates_filepaths
    ]);

    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
    exit;
    
  } catch (Exception $e) {
    error_log("Application submission failed: " . $e->getMessage());
    $_SESSION['form_errors'] = ["Προέκυψε ένα κρίσιμο σφάλμα. Η αίτησή σας δεν υποβλήθηκε. Παρακαλώ προσπαθήστε ξανά."];
    header('Location: application.php');
    exit;
  }
?>