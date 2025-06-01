<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="el">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../styles/header.css" />
    <link rel="stylesheet" href="../styles/index.css" />
    <link rel="stylesheet" href="../styles/footer.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet"
    />
    <script src="../scripts/header.js"></script>
    <title>ErasmApp</title>
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
            <li><a href="more.html">Περισσότερες Πληροφορίες</a></li>
            <li><a href="reqs.html">Απαιτήσεις</a></li>
            <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
              <li><a href="application.php">Δήλωση</a></li>
              <li><a href="profile.php">Προφίλ (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
              <li><a href="logout.php">Αποσύνδεση</a></li>
            <?php else: ?>
              <li><a href="sign-up.html">Εγγραφή</a></li>
              <li><a href="login.html">Σύνδεση</a></li>
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
      <section class="main-title">
        Πρόγραμμα Erasmus<br />Τμήμα Πληροφορικής & Τηλεπικοινωνιών<br />Πανεπιστήμιο
        Πελοποννήσου
      </section>
      <main>
        Καλωσορίσατε στο Πρόγραμμα Erasmus στο Τμήμα Πληροφορικής &
        Τηλεπικοινωνιών! Ξεκινήστε ένα μεταμορφωτικό ταξίδι ακαδημαϊκής
        αριστείας και πολιτιστικής ανακάλυψης. Σπουδάστε στο εξωτερικό σε
        διάσημα ευρωπαϊκά πανεπιστήμια κερδίζοντας μόρια για το πτυχίο σας.
        <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
          <a href="application.php">Ξεκινήστε το ταξίδι σας σήμερα</a>
        <?php else: ?>
          <p>Παρακαλούμε <a href="login.php">συνδεθείτε</a> ή <a href="sign-up.php">εγγραφείτε</a> για να συνεχίσετε.</p>
        <?php endif; ?>
      </main>
      <aside class="unistudents-photo">
        <img src="../media/erasmus-photo.png" alt="Photo of Erasmus" />
      </aside>
      <section class="unis-info">
        <div class="section-title">Συνεργαζόμενα Πανεπιστήμια: <br /></div>
        <div class="unis-info-text">
          <ul>
            <li>
              <a
                href="https://www.ox.ac.uk/students/fees-funding/international/erasmus"
              >
                University of Oxford</a
              >
            </li>
            <li>
              <a href="https://www.imperial.ac.uk/placements/erasmus/">
                Imperial College London</a
              >
            </li>
            <li>
              <a href="https://www.alexu.edu.eg/index.php/en/">
                Alexandria University</a
              >
            </li>
            <li>
              <a href="https://web.ub.edu/en/web/ub/"
                >University of Barcelona</a
              >
            </li>
            <li>
              <a href="https://www.hu-berlin.de/en">Humboldt University</a>
            </li>
          </ul>
        </div>
      </section>
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
    </div>
  </body>
</html>
