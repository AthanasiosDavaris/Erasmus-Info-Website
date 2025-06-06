<?php
  session_start();
  require_once 'db_config.php';

  if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php?error=unauthorized");
    exit;
  }

  $user_data = null;
  $universities = [];
  $error_message = '';

  $db_info = "mysql:host=$db_host;dbname=$db_name;charset=utf8";
  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ];

  try {
    $pdo = new PDO($db_info, $db_user, $db_password, $options);

    $user_statement = $pdo->prepare("SELECT first_name, last_name, student_id FROM users WHERE id = :id");
    $user_statement->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    $user_statement->execute();
    $user_data = $user_statement->fetch();

    $universities_statement = $pdo->query("SELECT name FROM universities ORDER BY name ASC");
    $universities = $universities_statement->fetchAll(PDO::FETCH_COLUMN);

    if (!$user_data) {
      $error_message = "Προέκυψε σφάλμα κατά την ανάκτηση των δεδομένων του χρήστη. Παρακαλώ δοκιμάστε ξανά αργότερα.";
    }

  } catch (PDOException $e) {
    error_log("Database Connection Error: " . $e->getMessage());
    $error_message = "Προέκυψε σφάλμα σύνδεσης με τη βάση δεδομένων. Παρακαλώ δοκιμάστε ξανά αργότερα.";
  }
?>

<!DOCTYPE html>
<html lang="el">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../styles/header.css" />
    <link rel="stylesheet" href="../styles/application.css" />
    <link rel="stylesheet" href="../styles/footer.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet"
    />
    <script src="../scripts/header.js"></script>
    <script src="../scripts/application.js"></script>
    <title>ErasmApp Applications</title>
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
      <main>
        <div class="hero-title">Αίτηση για Πρόγραμμα Erasmus+</div>
        <div class="hero-text">
          Συμπληρώστε την παρακάτω φόρμα για να υποβάλετε την αίτησή σας για
          ανταλλαγή Erasmus+. Η υποβολή σας θα αξιολογηθεί με βάση τις
          ακαδημαϊκές επιδόσεις και τις προτιμήσεις σας. Βεβαιωθείτε ότι όλα τα
          δεδομένα είναι ακριβή και πλήρη
          <br /><br />Τα πεδία με θαυμαστικό &#9888; είναι υποχρεωτικά.
          <br />Για ερωτήσεις, επικοινωνήστε με το Γραφείο Erasmus:
          <a href="mailto:erasmus@dit.uop.gr">erasmus@dit.uop.gr</a>
        </div>
      </main>

      <section class="application">
        <?php if (!empty($error_message)): ?>
          <div class="error-message">
            <?php echo $error_message; ?>
          </div>
        <?php else: ?>
          <?php
            if (isset($_SESSION['form_errors']) && !empty($_SESSION['form_errors'])) {
              echo '<div class="error-message">';
              echo '<strong>Η υποβολή απέτυχε. Παρακαλώ διορθώστε τα παρακάτω σφάλματα:</strong><br><ul>';
              foreach ($_SESSION['form_errors'] as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
              }
              echo '</ul></div>';
              unset($_SESSION['form_errors']);
            } 
          ?>
          <form class="application-form" action="submit_application.php" method="post" enctype="multipart/form-data">
            <fieldset>
              <legend>Προσωπικά Στοιχεία</legend>

              <div class="form-box">
                <label>Όνομα &#9888;</label>
                <input
                  type="text"
                  name="firstname"
                  value="<?php echo htmlspecialchars($user_data['first_name'] ?? ''); ?>"
                  readonly
                />
              </div>

              <div class="form-box">
                <label>Επίθετο &#9888;</label>
                <input
                  type="text"
                  name="lastname"
                  value="<?php echo htmlspecialchars($user_data['last_name'] ?? ''); ?>"
                  readonly
                />
              </div>

              <div class="form-box">
                <label>Αριθμός Μητρώου &#9888;</label>
                <input 
                  type="text" 
                  name="student_id" 
                  value="<?php echo htmlspecialchars($user_data['student_id'] ?? ''); ?>"
                  readonly
                >
              </div>
            </fieldset>

            <fieldset>
              <legend>Ακαδημαϊκά Στοιχεία</legend>

              <div class="form-box">
                <label>Ποσοστό περασμένων μαθημάτων (%) &#9888;</label>
                <input
                  type="number"
                  name="percentage_passed_lessons"
                  min="0"
                  max="100"
                  required
                  placeholder="Π.χ. 72"
                />
              </div>

              <div class="form-box">
                <label>Μέσος Όρος &#9888;</label>
                <input
                  type="number"
                  name="gpa"
                  step="0.01"
                  min="0"
                  max="10"
                  required
                  placeholder="Π.χ 6.5"
                />
              </div>
            </fieldset>

            <fieldset>
              <legend>Γλωσσικές Ικανότητες</legend>

              <div class="form-box">
                <label>Επίπεδο Αγγλικών &#9888;</label>
                <div class="radio-group">
                  <label><input type="radio" name="english_level" value="A1" required />A1</label>
                  <label><input type="radio" name="english_level" value="A2" />A2</label>
                  <label><input type="radio" name="english_level" value="B1" />B1</label>
                  <label><input type="radio" name="english_level" value="B2" />B2</label>
                  <label><input type="radio" name="english_level" value="C1" /> C1</label>
                  <label><input type="radio" name="english_level" value="C2" /> C2</label>
                </div>
              </div>

              <div class="form-box">
                <label>Γνώση άλλων γλωσσών; &#9888;</label>
                <div class="radio-group">
                  <label><input type="radio" name="other_languages" value="yes" required />Ναι</label>
                  <label><input type="radio" name="other_languages" value="no" />Όχι</label>
                </div>
              </div>
            </fieldset>

            <fieldset>
              <legend>Προτιμήσεις Πανεπιστημίων</legend>

              <div class="form-box">
                <label>1η Επιλογή &#9888;</label>
                <select name="university1" required>
                  <option value="">Επιλέξτε Πανεπιστήμιο</option>
                  <?php foreach ($universities as $uni): ?>
                    <option value="<?php echo htmlspecialchars($uni); ?>"><?php echo htmlspecialchars($uni); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-box">
                <label>2η Επιλογή</label>
                <select name="university2">
                  <option value="">Επιλέξτε Πανεπιστήμιο (προαιρετικό)</option>
                  <?php foreach ($universities as $uni): ?>
                    <option value="<?php echo htmlspecialchars($uni); ?>"><?php echo htmlspecialchars($uni); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-box">
                <label>3η Επιλογή</label>
                <select name="university3">
                  <option value="">Επιλέξτε Πανεπιστήμιο (προαιρετικό)</option>
                  <?php foreach ($universities as $uni): ?>
                    <option value="<?php echo htmlspecialchars($uni); ?>"><?php echo htmlspecialchars($uni); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </fieldset>

            <fieldset>
              <legend>Επίσημα Έγγραφα</legend>

              <div class="form-box">
                <label>Αναλυτική Βαθμολογία (PDF) &#9888;</label>
                <input
                  type="file"
                  name="grades_file"
                  accept=".pdf"
                  required
                />
              </div>

              <div class="form-box">
                <label>Πτυχίο Αγγλικών (PDF) &#9888;</label>
                <input
                  type="file"
                  name="english_certificate_file"
                  accept=".pdf"
                  required
                />
              </div>

              <div class="form-box">
                <label>Πτυχία άλλων ξένων γλωσσών (PDF)</label>
                <input
                  type="file"
                  name="other_languages_certificates_files[]"
                  accept=".pdf"
                  multiple
                />
              </div>
            </fieldset>

            <div class="terms-box">
              <label>
                <input type="checkbox" name="terms" required />
                Αποδέχομαι τους
                <a href="terms.php" target="_blank" rel="noopener noreferrer">
                  Όρους Συμμετοχής
                </a> &#9888;
              </label>
            </div>

            <button type="submit">
              Υποβολή Αίτησης!!
            </button>
          </form>
        <?php endif; ?>
      </section>
    </div>

    <div class="confirm-message" id="confirmMessage">
      <div class="confirm-content">
        <div class="confirm-text">
          &#10003; Η αίτησή σας υποβλήθηκε με επιτυχία!
        </div>

        <button class="return-button" onclick="window.location.href='index.php'">
          Επιστροφή στην Αρχική
        </button>
      </div>
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
