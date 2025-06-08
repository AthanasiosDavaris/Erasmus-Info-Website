<?php
  session_start();
  require_once 'db_config.php';

  if(!isset($_SESSION['login']) || $_SESSION['login'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=unauthorized");
    exit;
  }

  $db_info = "mysql:host=$db_host;dbname=$db_name;charset=utf8";
  $pdo = new PDO($db_info, $db_user, $db_password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $message = '';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;

    $statement = $pdo->prepare("UPDATE application_settings SET start_date = :start_date, end_date = :end_date WHERE id = 1");
    $statement->execute(['start_date' => $start_date, 'end_date' => end_date]);

    $message = "Η περίοδος αιτήσεων ενημερώθηκε με επιτυχία!";
  }

  $current_settings = $pdo->query("SELECT start_date, end_date FROM application_settings WHERE id = 1")->fetch();
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
  <title>ErasmApp Περίοδος Αιτήσεων</title>
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
    <h1>Πίνακας Ελέγχou</h1>

    <h2>Καθορισμός Περιόδου Υποβολής Αιτήσεων</h2>
    <p>Ορίστε τις ημερομηνίες έναρξης και λήξης για την υποβολή αιτήσεων Erasmus.</p>

    <?php if ($message): ?>
      <div class="success-message"><?php echo $message ?></div>
    <?php endif; ?>

    <form action="manage_application_period" method="POST">
      <div class="form-group">
        <label for="start_date">Ημερομηνία Έναρξης:</label>
        <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($current_settings['start_date'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label for="end_date">Ημερομηνία Λήξης:</label>
        <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($current_settings['end_date'] ?? '') ?>">
      </div>
      <p><small>Αφήστε τα πεδία κενά για να απενεργοποιήσετε την περίοδο υποβολής.</small></p>
      <button type="submit" class="admin-button">Αποθήκευση Περιόδου</button>
    </form>

    <a href="admin_dashboard.php" class="back-link">Επιστροφή στον Πίνακα Ελέγχου</a>
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