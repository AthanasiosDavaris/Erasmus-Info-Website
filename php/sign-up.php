<?php
  session_start();
  require_once 'db_config.php';

  $first_name = '';
  $last_name = '';
  $student_id = '';
  $phone = '';
  $email = '';
  $username = '';
  $confirm_password = '';
  $terms = false;

  $errors = [];
  $error_message = '';

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
      $errors['student_id'] = "Ο αριθμός μητρώου πρέπει να αποτελείται από 13 ψηφία, να ξεκινάει με '2022' και να περιέχει μόνο ψηφία.";
    }

    if (empty($phone)) {
      $errors['phone'] = "Το τηλέφωνο είναι υποχρεωτικό.";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
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
            mysqli_stmt_close($insert_user_token);
            mysqli_close($con);
            header("Location: sign-up-success.php");
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
      $error_message = "Η εγγραφή απέτυχε. Παρακαλώ διορθώστε τα σφάλματα και προσπαθήστε ξανά.";
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../styles/header.css" />
    <link rel="stylesheet" href="../styles/sign-up.css" />
    <link rel="stylesheet" href="../styles/footer.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet"
    />
    <script src="../scripts/header.js"></script>
    <script src="../scripts/sign-up.js"></script>
    <title>ErasmApp Εγγραφή</title>
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
              <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li><a href="admin_dashboard.php"></a>Πίνακας Ελέγχου</li>
              <?php else: ?>
                <li><a href="profile.php">Προφίλ (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
              <?php endif; ?>
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
      <main>
        <div class="hero-title">Νέος Λογαριασμός</div>
        <div class="hero-text">
          Δημιουργία ενός νέου ονόματος χρήστη και κωδικού πρόσβασης για σύνδεση
          στην πλατφόρμα Erasmus
          <br /><br />Τα πεδία με θαυμαστικό &#9888; είναι υποχρεωτικά.
        </div>
      </main>

      <section class="sign-up-container">
        <form id="sign-up-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>

          <?php if (!empty($error_message)): ?>
            <div class="error-main-message">
              <?php echo htmlspecialchars($error_message); ?>
            </div>
          <?php endif; ?>
          <?php if (isset($errors['database'])): ?>
            <div class="error-main-message"><?php echo htmlspecialchars($errors['database']); ?></div>
          <?php endif; ?>
          <div class="form-container">
            <div class="form-group">
              <div class="form-box">
                <label for="firstName">Όνομα &#9888;</label>
                <input
                  type="text"
                  id="firstName"
                  name="firstName"
                  required
                  placeholder="Εισάγετε το όνομά σας"
                  pattern="^[^0-9]*$"
                  value="<?php echo htmlspecialchars($first_name); ?>"
                />
                <span class="error-message" id="firstNameError"><?php echo htmlspecialchars($errors['firstName'] ?? ''); ?></span>
              </div>

              <div class="form-box">
                <label for="lastName">Επώνυμο &#9888;</label>
                <input
                  type="text"
                  id="lastName"
                  name="lastName"
                  required
                  placeholder="Εισάγετε το επώνυμό σας"
                  pattern="^[^0-9]*$"
                  value="<?php echo htmlspecialchars($last_name); ?>"
                />
                <span class="error-message" id="lastNameError"><?php echo htmlspecialchars($errors['lastName'] ?? ''); ?></span>
              </div>

              <div class="form-box">
                <label for="studentId">Αριθμός Μητρώου &#9888;</label>
                <input
                  type="text"
                  id="studentId"
                  name="studentId"
                  required
                  pattern="2022[0-9]{9}"
                  placeholder="Π.χ. 2022202200150"
                  value="<?php echo htmlspecialchars($student_id); ?>"
                />
                <span class="error-message" id="studentIdError"><?php echo htmlspecialchars($errors['studentId'] ?? ''); ?></span>
              </div>

              <div class="form-box">
                <label for="phone">Τηλέφωνο &#9888;</label>
                <input
                  type="tel"
                  id="phone"
                  name="phone"
                  required
                  pattern="[0-9]{10}"
                  placeholder="Π.χ. 6912345780"
                  value="<?php echo htmlspecialchars($phone); ?>"
                />
                <span class="error-message" id="phoneError"><?php echo htmlspecialchars($errors['phone'] ?? ''); ?></span>
              </div>
            </div>

            <div class="form-group">
              <div class="form-box">
                <label for="email">Διεύθυνση mail &#9888;</label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  required
                  placeholder="example@go.uop.gr"
                  value="<?php echo htmlspecialchars($email); ?>"
                />
                <span class="error-message" id="emailError"><?php echo htmlspecialchars($errors['email'] ?? ''); ?></span>
              </div>

              <div class="form-box">
                <label for="username">Όνομα Χρήστη &#9888;</label>
                <input
                  type="text"
                  id="username"
                  name="username"
                  required
                  placeholder="Διαλέξτε ένα μοναδικό username"
                  value="<?php echo htmlspecialchars($username); ?>"
                />
                <span class="error-message" id="usernameError"><?php echo htmlspecialchars($errors['username'] ?? ''); ?></span>
              </div>

              <div class="form-box">
                <label for="password">Κωδικός Πρόσβασης &#9888;</label>
                <input
                  type="password"
                  id="password"
                  name="password"
                  required
                  placeholder="Τουλάχιστον 5 χαρακτήρες, 1 σύμβολο"
                  pattern="(?=.*[!@#$%^&*()_+\-=\[\]{};':\\|,.&lt;>\/?~`]).{5,}"
                />
                <span class="error-message" id="passwordError"><?php echo htmlspecialchars($errors['password'] ?? ''); ?></span>
                <div class="password-reqs">
                  Ο κωδικός πρέπει να περιέχει:
                  <ul>
                    <li>Τουλάχιστον 5 χαρακτήρες</li>
                    <li>Τουλάχιστον 1 ειδικό χαρακτήρα (π.χ. !, #, $)</li>
                  </ul>
                </div>
              </div>

              <div class="form-box">
                <label for="confirmPassword">Επιβεβαίωση Κωδικού &#9888;</label>
                <input
                  type="password"
                  id="confirmPassword"
                  name="confirmPassword"
                  required
                  placeholder="Εισάγετε ξανά τον κωδικό σας"
                />
                <span class="error-message" id="confirmPasswordError"><?php echo htmlspecialchars($errors['confirmPassword'] ?? ''); ?></span>
              </div>
            </div>
          </div>

          <div id="formMainError" class="error-main-message"></div>

          <div class="form-terms">
            <input type="checkbox" id="terms" name="terms" required <?php if ($terms) echo 'checked'; ?>/>
            <label for="terms">
              Αποδέχομαι τους
              <a href="terms.php" target="_blank" rel="noopener noreferrer"
                >Όρους Χρήσης</a
              >
              και την
              <a
                href="privacy-policy.php"
                target="_blank"
                rel="noopener noreferrer"
                >Πολιτική Απορρήτου
              </a>
            </label>
            <span class="error-message" id="termsError"><?php echo htmlspecialchars($errors['terms'] ?? ''); ?></span>
          </div>

          <div class="form-redirect-to-login">
            <b>Έχετε ήδη λογαριασμό;</b><br />
            <a href="login.php">Συνδεθείτε εδώ</a> για να αποκτήσετε πρόσβαση
            στο προφίλ σας.
          </div>

          <div class="form-buttons">
            <button type="submit" class="submit-button">
              Δημιουργία Λογαριασμού
            </button>
            <button type="reset" class="cancel-button">Άκυρο</button>
          </div>
        </form>
      </section>
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
