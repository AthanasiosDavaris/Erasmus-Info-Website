<?php
session_start();

require_once 'db_config.php';

$db_info = "mysql:host=$db_host;dbname=$db_name;charset=utf8";
$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
];

try {
  $pdo = new PDO($dsn, $db_user, $db_password, $options);
} catch (PDOException $e) {
  error_log("Database Connection Error: " . $e->getMessage());
  header("Location: login.php?error=3");
  exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = isset($_POST['username']) ? trim($_POST['username']) : '';
  $password = isset($_POST['password']) ? $_POST['username'] : '';

  if (empty($username) || empty($password)) {
    header("Location: login.php?error=2");
    exit;
  }

  try {
    $statement = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :username");
    $statement->bindParam(':username', $username, PDO::PARAM_STR);
    $statement->execute();
    $user = $statement->fetch();

    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['login'] = true;
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];
      session_regenerate_id(true);

      header("Location: index.php");
      exit;
    } else {
      header("Location: login.php?error=1");
      exit;
    }
  } catch (PDOException $e) {
    error_log("Login Query Error: " . $e->getMessage());
    header("Location: login.php?error=3");
    exit;
  }
} else {
  header("Location: login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../styles/header.css" />
    <link rel="stylesheet" href="../styles/login.css" />
    <link rel="stylesheet" href="../styles/footer.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet"
    />
    <script src="../scripts/header.js"></script>
    <script src="../scripts/login.js"></script>
    <title>ErasmApp Login</title>
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
        <div class="hero-title">Σύνδεση</div>
        <div class="hero-text">
          Εισάγετε τα στοιχεία σας για να συνδεθείτε στην πλατφόρμα Erasmus
        </div>
        <div id="messageArea"></div>
      </main>

      <section class="login-container">
        <form action="login.php" method="post">
          <div class="login-form">
            <div class="form-box">
              <label for="username">Όνομα Χρήστη</label>
              <input
                type="text"
                id="username"
                name="username"
                required
                placeholder="Π.χ. john_doe2023"
              />
            </div>

            <div class="form-box">
              <label for="password">Κωδικός Πρόσβασης</label>
              <input
                type="password"
                id="password"
                name="password"
                required
                placeholder="Εισάγετε τον κωδικό σας"
              />
            </div>

            <div class="form-buttons">
              <button type="submit" class="submit-button">Σύνδεση</button>
            </div>

            <div class="additional-links">
              <a href="forgot_password.php">Ξεχάσατε τον κωδικό σας;</a><br />
              <div>
                Δεν έχετε λογαριασμό;
                <a href="sign-up.php">Δημιουργία λογαριασμού</a>
              </div>
            </div>
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
