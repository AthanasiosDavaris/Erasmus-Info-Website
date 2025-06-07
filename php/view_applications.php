<?php
  session_start();
  require_once 'db_config.php';

  if(!isset($_SESSION['login']) || $_SESSION['login'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=unauthorized");
    exit;
  }

  $applications = [];
  $error_message = '';

  try {
    $db_info = "mysql:host=$db_host;dbname=$db_name;charset=utf8";
    $pdo = new PDO($db_info, $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $statement = $pdo->query("SELECT * FROM applications ORDER BY submission_date DESC");
    $applications = $statement->fetchAll();

  } catch (PDOException $e) {
    $error_message = "Σφάλμα σύνδεσης με τη βάση δεδομένων: " . $e->getMessage();
  }
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../styles/header.css" />
  <link rel="stylesheet" href="../styles/admin.css" />
  <link rel="stylesheet" href="../styles/footer.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet"
  />
  <script src="../scripts/header.js"></script>
  <script src="../scripts/profile.js"></script>
  <title>ErasmApp Προβολή Αιτήσεων</title>
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
    <h1>Προβολή Αιτήσεων</h1>

    <a href="admin_dashboard.php" class="back-link">Επιστροφή στον Πίνακα Ελέγχου</a>

    <?php if ($error_message): ?>
      <div class="error-message"><?php echo $error_message ?></div>
    <?php elseif (empty($applications)): ?>
      <div class="info-message">Δεν έχουν υποβληθεί αιτήσεις ακόμα.</div>
    <?php else: ?>
      <table class="applications-table">
        <thead>
          <tr>
            <th>Ημερομηνία</th>
            <th>Ονοματεπώνυμο</th>
            <th>ΑΜ</th>
            <th>Μέσος Όρος</th>
            <th>Επίπεδο Αγγλικών</th>
            <th>Επιλογές Πανεπιστημίων</th>
            <th>Έγγραφα</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($applications as $app): ?>
            <tr>
              <td><?php echo (new DateTime($app['submission_date']))->format('d/m/Y H:i'); ?></td>
              <td><?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?></td>
              <td><?php echo htmlspecialchars($app['student_id']); ?></td>
              <td><?php echo htmlspecialchars($app['gpa']); ?></td>
              <td><?php echo htmlspecialchars($app['english_level']); ?></td>
              <td>
                1. <?php echo htmlspecialchars($app['university1']); ?><br>
                2. <?php echo htmlspecialchars($app['university2'] ?? 'N/A'); ?><br>
                3. <?php echo htmlspecialchars($app['university3'] ?? 'N/A'); ?>
              </td>
              <td>
                <a href="<?php echo htmlspecialchars($app['grades_filepath']); ?>" target="_blank">Αναλυτική Βαθμολογία</a><br>
                <a href="<?php echo htmlspecialchars($app['english_certificate_filepath']); ?>" target="_blank">Πτυχίο Αγγλικών</a><br>
                <?php
                  if (!empty($app['other_languages_certificates_filepaths'])) {
                    $other_files = json_decode($app['other_languages_certificates_filepaths'], true);
                    if (is_array($other_files)) {
                      foreach ($other_files as $index => $file_path) {
                        echo '<a href="' . htmlspecialchars($file_path) . '" target="_blank">Άλλο Πτυχίο ' . ($index + 1) . '</a><br>';
                      }
                    }
                  }
                ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
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