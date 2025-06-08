<?php
  session_start();
  require_once 'db_config.php';

  if(!isset($_SESSION['login']) || $_SESSION['login'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=unauthorized");
    exit;
  }

  $db_info = "mysql:host=$db_host;dbname=$db_name;charset=utf8";
  $pdo = new PDO($db_info, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['publish'])) {
      $pdo->query("UPDATE application_settings SET results_published = 'yes' WHERE id = 1");
    } elseif (isset($_POST['unpublish'])) {
      $pdo->query("UPDATE application_settings SET results_published = 'no' WHERE id = 1");
    }
    header('Location: results.php');
    exit;
  }

  $settings = $pdo->query("SELECT end_date, results_published FROM application_settings WHERE id = 1")->fetch();
  $accepted_applications = $pdo->query("SELECT first_name, last_name, university1 FROM applications WHERE status = 'accepted' ORDER BY last_name ASC")->fetchAll();

  $has_application_period_ended = false;
  if (!empty($settings['end_date'])) {
    $end_date = new DateTime($settings['end_date']);
    $current_date = new DateTime();
    if ($current_date > $end_date) {
      $has_application_period_ended = true;
    }
  }
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../styles/header.css" />
  <link rel="stylesheet" href="../styles/admin.css?v=<?php echo filemtime('../styles/admin.css'); ?>" />
  <link rel="stylesheet" href="../styles/footer.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet"
  />
  <script src="../scripts/header.js"></script>
  <script src="../scripts/profile.js"></script>
  <title>ErasmApp Ανακοίνωση Αποτελεσμάτων</title>
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
                <li><a href="admin_dashboard.php">Πίνακας Ελέγχου</a></li>
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

  <main>
    <a href="admin_dashboard.php" class="back-link">Επιστροφή στον Πίνακα Ελέγχou</a>
    <h2>Ανακοίνωση Αποτελεσμάτων Erasmus+</h2>

    <?php if ($settings['results_published'] === 'yes'): ?>
      <div class="success-message"><strong>Η κατάσταση είναι:</strong>Τα αποτελέσματα είναι ΔΗΜΟΣΙΕΥΜΕΝΑ.</div>
    <?php else: ?>
      <div class="info-message"><strong>Η κατάσταση είναι:</strong> Τα αποτελέσματα ΔΕΝ είναι δημοσιευμένα.</div>
    <?php endif; ?>

    <hr class="form-divider">

    <h3>Προεπισκόπηση Αποτελεσμάτων</h3>
    <p>Παρακάτω εμφανίζονται οι αιτήσεις που έχουν χαρακτηριστεί ως "Δεκτές". Αυτή η λίστα θα δημοσιευθεί.</p>

    <?php if (empty($accepted_applications)): ?>
      <div class="info-message">Δεν υπάρχουν αιτήσεις που να έχουν χαρακτηριστεί ως "Δεκτές".</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="applications-table">
          <thead>
            <tr>
              <th>Επίθετο</th>
              <th>Όνομα</th>
              <th>1η Επιλογή Πανεπιστημίου</th>
            </tr> 
          </thead>
          <tbody>
            <?php foreach ($accepted_applications as $app): ?>
              <tr>
                <td><?php echo htmlspecialchars($app['last_name']); ?></td>
                <td><?php echo htmlspecialchars($app['first_name']); ?></td>
                <td><?php echo htmlspecialchars($app['university1']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

    <hr class="form-divider">

    <h3>Ενέργειες</h3>
    <?php if (!$has_application_period_ended): ?>
      <p><strong>Η περίοδος υποβολής αιτήσεων δεν έχει λήξει ακόμα (λήγει στις <?php echo htmlspecialchars($settings['end_date']); ?>).</strong></p>
      <p>Δεν μπορείτε να δημοσιεύσετε τα αποτελέσματα μέχρι να παπεράσει αυτή η ημερομηνία.</p>
      <button class="admin-button" disabled>Δημοσίευση Αποτελεσμάτων</button>
    <?php else: ?>
      <form action="results.php" method="POST" class="publish-results">
        <?php if ($settings['results_published'] === 'no'): ?>
          <button type='submit' name='publish' class="admin-button">Δημοσίευση Αποτελεσμάτων</button>
          <p><small>Πατώντας αυτό το κουμπί, η παραπάνω λίστα θα εμφανιστεί δημόσια στη σελίδα "Περισσότερες Πληροφορίες".</small></p>
        <?php else: ?>
          <button type="submit" name="unpublished" class="unpublish-button">Απόσυρση Αποτελεσμάτων</button>
          <p><small>Πατώντας αυτό το κουμπί, η λίστα θα αφαιρεθεί από τη δημόσια σελίδα.</small></p>
        <?php endif; ?>
      </form>
    <?php endif; ?>
  </main>

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