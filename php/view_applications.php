<?php
  session_start();
  require_once 'db_config.php';

  if(!isset($_SESSION['login']) || $_SESSION['login'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=unauthorized");
    exit;
  }

  $db_info = "mysql:host=$db_host;dbname=$db_name;charset=utf8";
  $pdo = new PDO($db_info, $db_user, $db_password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $accepted = isset($_POST['accepted']) ? array_keys($_POST['accepted']) : [];
    $displayed_ids = isset($_POST['displayed_ids']) ? $_POST['displayed_ids'] : [];

    try {
      $pdo->beginTransaction();

      $accept_statement = $pdo->prepare("UPDATE applications SET status = 'accepted' WHERE id = ?");
      $pending_statement = $pdo->prepare("UPDATE applications SET status = 'pending' WHERE id = ?");

      foreach ($displayed_ids as $id) {
        if (in_array($id, $accepted)) {
          $accept_statement->execute([$id]);
        } else {
          $pending_statement->execute([$id]);
        }
      }
      $pdo->commit();
    } catch (Exception $e) {
      $pdo->rollBack();
      die("An error occurred while updating statuses: " . $e->getMessage());
    }

    $query_string = http_build_query($_GET);
    header('Location: view_applications.php' . ($query_string ? '?' . $query_string : ''));
    exit;
  }

  $applications = [];
  $universities = [];
  $error_message = '';

  try {
    $universities = $pdo->query("SELECT university_name FROM universities ORDER BY university_name ASC")->fetchAll();

    $min_percentage = isset($_GET['min_percentage']) && is_numeric($_GET['min_percentage']) ? (int)$_GET['min_percentage'] : null;
    $selected_university = isset($_GET['university']) ? trim($_GET['university']) : '';

    $sql = "SELECT * FROM applications";
    $where_clauses = [];
    $parameters = [];

    if ($min_percentage !== null) {
      $where_clauses[] = "percentage_passed_lessons >= ?";
      $parameters[] = $min_percentage;
    }
    if (!empty($selected_university)) {
      $where_clauses[] = "(university1 = ? OR university2 = ? OR university3 = ?)";
      array_push($parameters, $selected_university, $selected_university, $selected_university);
    }

    if (!empty($where_clauses)) {
      $sql .= " WHERE " . implode(' AND ', $where_clauses);
    }
    $sql .= " ORDER BY gpa DESC";

    $statement = $pdo->prepare($sql);
    $statement->execute($parameters);
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

    <form action="view_applications.php" method="get" class="filter-form">
      <h3>Φίλτρα Αναζήτησης</h3>
      <div class="filter-controls">
        <div class="form-group">
          <label for="min_percentage">Ελάχιστο Ποσοστό Περασμένων Μαθημάτων (%):</label>
          <input type="number" id="min_percentage" name="min_percentage" min="0" max="100" placeholder="Π.χ. 70" value="<?php echo htmlspecialchars($min_percentage ?? ''); ?>">
        </div>
        <div class="form-group">
          <label for="university">Φιλτράρισμα ανά Πανεπιστήμιο:</label>
          <select name="university" id="university">
            <option value="">-- Όλα τα Πανεπιστήμια --</option>
            <?php foreach ($universities as $uni): ?>
              <option value="<?php echo htmlspecialchars($uni['university_name']); ?>" <?php echo ($uni['university_name'] === $selected_university) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($uni['university_name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="filter-buttons">
        <button type="submit" class="admin-button">Εφαρμογή Φίλτρων</button>
        <a href="view_applications.php" class="clear-filter-link">Καθαρισμός Φίλτρων</a>
      </div>
    </form>

    <hr class="form-divider">

    <form action="view_applications.php?<?php echo http_build_query($_GET); ?>" method="post">
      <h3>Αιτήσεις (Ταξινόμηση κατά φθίνοντα Μ.Ο.)</h3>
      <?php if ($error_message): ?>
        <div class="error-message"><?php echo $error_message ?></div>
      <?php elseif (empty($applications)): ?>
        <div class="info-message">Δεν βρέθηκαν αιτήσεις που να ταιριάζουν με τα κριτήρια αναζήτησης.</div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="applications-table">
            <thead>
              <tr>
                <th class="status-col">Δεκτή</th>
                <th>Ονοματεπώνυμο</th>
                <th>ΑΜ</th>
                <th>Μέσος Όρος</th>
                <th>Περασμένα (%)</th>
                <th>Επιλογές</th>
                <th>Έγγραφα</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($applications as $app): ?>
                <input type="hidden" name="displayed_ids[]" value="<?php echo $app['id']; ?>">
                <tr>
                  <td>
                    <input type="checkbox" name="accepted[<?php echo $app['id']; ?>]" class="status-checkbox" <?php echo ($app['status'] === 'accepted') ? 'checked' : '' ?>>
                  </td>
                  <td><?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?></td>
                  <td><?php echo htmlspecialchars($app['student_id']); ?></td>
                  <td><strong><?php echo htmlspecialchars($app['gpa']); ?></strong></td>
                  <td><?php echo htmlspecialchars($app['percentage_passed_lessons']); ?>%</td>
                  <td>
                    1. <?php echo htmlspecialchars($app['university1']); ?><br>
                    1. <?php echo htmlspecialchars($app['university2']); ?><br>
                    3. <?php echo htmlspecialchars($app['university3']); ?><br>
                  </td>
                  <td>
                    <a href="<?php echo htmlspecialchars($app['grades_filepath']); ?>" target="_blank">Αναλυτική Βαθμολογία</a><br>
                    <a href="<?php echo htmlspecialchars($app['english_certificate_filepath']); ?>" target="_blank">Πτυχίο Αγγλικών</a><br>
                    <?php
                    if (!empty($app['other_languages_certificates_filepaths'])) {
                      $other_files = json_decode($app['other_languages_certificates_filepaths'], true);
                      if (is_array($other_files)) {
                        foreach ($other_files as $index => $filepath) {
                          echo '<a href="' . htmlspecialchars($filepath) . '" target="_blank">Άλλο Πτυχίο ' . ($index + 1) . '</a><br>';
                        }
                      }
                    }
                    ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="update-button-container">
          <button type="submit" name="update_status" class="admin-button">Αποθήκευση Κατάστασης</button>
        </div>
      <?php endif; ?>
    </form>
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