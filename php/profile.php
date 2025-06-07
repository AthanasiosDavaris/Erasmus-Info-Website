<?php
  session_start();
  require_once 'db_config.php';

  if(!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php?error=auth_required");
    exit;
  }

  $user_id = $_SESSION['user_id'];
  $username = htmlspecialchars($_SESSION['username']);
  $user_data = null;
  $error_message = '';
  $success_message = '';

  $db_info = "mysql:host=$db_host;dbname=$db_name;charset=utf8";
  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ];

  try {
    $pdo = new PDO($db_info, $db_user, $db_password, $options);

    $statement = $pdo->prepare("SELECT first_name, last_name, student_id, phone, email FROM users WHERE id = :id");
    $statement->bindParam(':id', $user_id, PDO::PARAM_INT);
    $statement->execute();
    $user_data = $statement->fetch();

    if (!$user_data) {
      $error_message = "Δεν ήταν δυνατή η ανάκτηση των στοιχείων του χρήστη.";
    }

  } catch (PDOException $e) {
    error_log("Profile Page DB Error: " . $e->getMessage());
    $error_message = "Προέκυψε σφάλμα κατά τη σύνδεση με τη βάση δεδομένων.";
  }

  if (isset($_GET['update'])) {
    if($_GET['update'] === 'success') {
      $success_message = "Το προφίλ ενημερώθηκε με επιτυχία!";
    } elseif($_GET['update'] === 'error') {
      $error_code = $_GET['error_msg'] ?? '';
      switch ($error_code) {
        case 'db_connection_error':
          $error_message = "Σφάλμα σύνδεσης με τη βάση δεδομένων κατά την ενημέρωση.";
          break;
        case 'db_update_failed':
          $error_message = "Προέκυψε σφάλμα κατά την αποθήκευση των αλλαγών.";
          break;
        case 'email_exists':
          $error_message = "Το email που δηλώσατε χρησιμοποιείται ήδη.";
          break;
        case 'user_not_found':
          $error_message = "Δεν βρέθηκε ο χρήστης για ενημέρωση.";
          break;
        default:
          $error_message = "ΤΠροέκυψε σφάλμα κατά την ενημέρωση του προφίλ.";
          if ($error_code) {
            $error_message .= " (" . htmlspecialchars($error_code) . ")";
          }
      }
    } elseif($_GET['update'] === 'validation') {
      $error_code = $_GET['error_msg'] ?? 'invalid_data';
      switch ($error_code) {
        case 'required_email_field':
          $error_message = "Το πεδίο Email είναι υποχρεωτικό και δεν μπορεί να είναι κενό.";
          break;
        case 'invalid_email':
          $error_message = "Η μορφή του email δεν είναι έγκυρη.";
          break;
        case 'password_mismatch':
          $error_message = "Οι νέοι κωδικοί πρόσβασης δεν ταιριάζουν.";
          break;
        case 'password_short':
          $error_message = "Ο νέος κωδικός πρέπει να είναι τουλάχιστον 8 χαρακτήρες.";
          break;
        case 'password_special_char':
          $error_message = "Ο νέος κωδικός πρόσβασης πρέπει να περιέχει τουλάχιστον έναν ειδικό χαρακτήρα.";
          break;
        case 'invalid_student_id':
          $error_message = "Ο αριθμός μητρώου πρέπει να αποτελείται από 13 ψηφία, να ξεκινάει με '2022' και να περιέχει μόνο ψηφία.";
          break;
        case 'invalid_phone':
          $error_message = "Το τηλέφωνο πρέπει να αποτελείται από ακριβώς 10 ψηφία.";
          break;
        case 'invalid_name':
          $error_message = "Το όνομα και το επώνυμο δεν πρέπει να περιέχουν αριθμούς.";
          break;
        default:
          $error_message = "Τα δεδομένα που υποβλήθηκαν δεν είναι έγκυρα. Παρακαλώ ελέγξτε τα πεδία.";
      }
    } elseif($_GET['update'] === 'no_changes') {
      $success_message = "Δεν έγιναν αλλαγές στα στοιχεία σας.";
    }
  }
?>

<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../styles/header.css" />
  <link rel="stylesheet" href="../styles/profile.css" />
  <link rel="stylesheet" href="../styles/footer.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet"
  />
  <script src="../scripts/header.js"></script>
  <script src="../scripts/profile.js"></script>
  <title>ErasmApp Προφίλ Χρήστη - <?php echo $username; ?></title>
</head>
<body>
  <header>
      <!-- Erasmus Logo -->
      <div class="erasmus-logo-container">
        <a href="index.php">
          <img
            src="../media/erasmus_logo.png"
            alt="Erasmus Logo"
            class="erasmus-logo"
          />
        </a>
      </div>

      <!-- Navigation Bar -->
      <div class="navbar">
        <nav>
          <ul>
            <li><a href="more.php">Περισσότερες Πληροφορίες</a></li>
            <li><a href="reqs.php">Απαιτήσεις</a></li>
            <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
              <li><a href="application.php">Δήλωση</a></li>
              <li><a href="profile.php">Προφίλ (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
              <li><a href="logout.php">Αποσύνδεση</a></li>
            <?php else: ?>
              <li><a href="sign-up.php">Εγγραφή</a></li>
              <li><a href="login.php">Σύνδεση</a></li>
            <?php endif; ?>
          </ul>
        </nav>
      </div>

      <!-- Navigation Bar Mobile version -->
      <div class="mobile-navbar" onclick="toggleMenu()">
        <div class="dropdown"></div>
      </div>
    </header>

    <div class="container">
      <div class="profile-container">
        <h1>Επεξεργασία Προφίλ του Χρήστη <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <p>Όνομα Χρήστη: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> (δεν μπορεί να αλλάξει)</p>
        <p>Αφήστε κενό ένα πεδίο (εκτός του email) για να μην αλλάξει. Το email είναι υποχρεωτικό (&#9888;).</p>
      </div>

      <div class="message-area">
        <?php if ($success_message): ?>
          <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
          <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
      </div>

      <?php if ($user_data): ?>
        <form id="profileUpdateForm" action="update_user_info.php" method="post" class="profile-form">
          <div class="form-box">
            <label for="first_name">Όνομα:</label>
            <input type="text" name="first_name" id="firstName" value="<?php echo htmlspecialchars($user_data['first_name'] ?? ''); ?>" data-original-value="<?php echo htmlspecialchars($user_data['first_name'] ?? ''); ?>" pattern="^[^0-9]*$">
          </div>

          <div class="form-box">
            <label for="last_name">Επίθετο:</label>
            <input type="text" name="last_name" id="lastName" value="<?php echo htmlspecialchars($user_data['last_name'] ?? ''); ?>" 
            data-original-value="<?php echo htmlspecialchars($user_data['last_name'] ?? ''); ?>" pattern="^[^0-9]*$">
          </div>

          <div class="form-box">
            <label for="student_id">Αριθμός Μητρώου:</label>
            <input type="text" name="student_id" id="studentId" value="<?php echo htmlspecialchars($user_data['student_id'] ?? ''); ?>"
            data-original-value="<?php echo htmlspecialchars($user_data['student_id'] ?? ''); ?>" pattern="2022[0-9]{9}">
          </div>

          <div class="form-box">
            <label for="phone">Τηλέφωνο:</label>
            <input type="tel" name="phone" id="phone" value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>"
            data-original-value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>" pattern="[0-9]{10}">
          </div>

          <div class="form-box">
            <label for="email">Email: &#9888;</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" 
            data-original-value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>"
            required title="Παρακαλώ εισάγετε μια έγκυρη διεύθυνση email.">
          </div>

          <hr>
          <h2>Αλλαγή Κωδικού Πρόσβασης</h2>

          <div class="form-box">
            <label for="newPassword">Νέος Κωδικός Πρόσβασης:</label>
            <input type="password" name="new_password" id="newPassword" placeholder="Εισάγετε τον νέο σας κωδικό" 
            pattern="(?=.*[!@#$%^&*()_+\-=\[\]{};':\\|,.&lt;>\/?~`]).{5,}" title="Τουλάχιστον 5 χαρακτήρες, 1 σύμβολο." data-original-value="">
            
            <div class="password-reqs">
              Ο κωδικός πρέπει να περιέχει:
              <ul>
                <li>Τουλάχιστον 5 χαρακτήρες</li>
                <li>Τουλάχιστον 1 ειδικό χαρακτήρα (π.χ. !, #, $)</li>
              </ul>
            </div>
          </div>

          <div class="form-box">
            <label for="confirmNewPassword">Επιβεβαίωση Νέου Κωδικού:</label>
            <input type="password" name="confirm_new_password" id="confirmNewPassword" placeholder="Επιβεβαιώστε τον νέο σας κωδικό">
          </div>

          <div class="form-buttons">
            <button type="submit" class="submit-button">Ενημέρωση Προφίλ</button>
          </div>
        </form>
      <?php else: ?>
        <p class="error-loading-data">Δεν ήταν δυνατή η φόρτωση των δεδομένων του προφίλ σας.</p>
      <?php endif; ?>
    </div>

    <footer>
        <section class="left-section">
          <a href="https://dit.uop.gr" target="_blank">
            <img
              src="../media/dit-uop-logo.jpg"
              alt="University of Peloponnese, Department of Informatics and Telecommunications"
            />
          </a>
        </section>
        <section class="mid-section">
          &#169; 2025 Thanos Davaris. All rights reserved.
        </section>
        <section class="right-section">
          <a
            href="https://github.com/AthanasiosDavaris"
            class="social-media-button"
            target="_blank"
          >
            <img src="../media/github.png" alt="github" />
          </a>
          <!-- Needs fix -->
          <a
            href="https://www.linkedin.com/in/athanasios-davaris-8483a9338"
            class="social-media-button"
            target="_blank"
          >
            <img src="../media/linkedin.png" alt="linkedin" />
          </a>
        </section>
      </footer>
</body>
</html>