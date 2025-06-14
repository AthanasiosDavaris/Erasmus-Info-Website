<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="el">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../styles/header.css" />
    <link rel="stylesheet" href="../styles/reqs.css" />
    <link rel="stylesheet" href="../styles/footer.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet"
    />
    <script src="../scripts/header.js"></script>
    <script src="../scripts/reqs.js"></script>
    <title>ErasmApp Απαιτήσεις</title>
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

    <div class="container">
      <main>
        <div class="hero-title">Ελάχιστες Απαιτήσεις</div>
        <div class="hero-text">
          Ονειρεύεστε να σπουδάσετε στο εξωτερικό με το Erasmus? Ξεκινήστε εδώ!
          <br />
          Ελέγξτε αν πληροίτε τις ελάχιστες απαιτήσεις για να υποβάλετε αίτηση
          για πρόγραμμα ανταλλαγής Erasmus+ στο Πανεπιστήμιο Πελοποννήσου.
        </div>
        <img
          class="hero-img"
          src="../media/reqs-main-img.jpeg"
          alt="Photo of Students Studying"
        />
        <p class="hero-p">
          Για να υποβάλετε αίτηση για ανταλλαγή Erasmus+, πρέπει να πληροίτε τις
          ακόλουθες ελάχιστες απαιτήσεις. Αυτά εξασφαλίζουν ότι οι φοιτητές
          είναι ακαδημαϊκά προετοιμασμένοι για τις προκλήσεις των σπουδών στο
          εξωτερικό. Ελέγξτε τα παρακάτω κριτήρια και χρησιμοποιήστε τον Γρήγορο
          έλεγχο καταλληλότητας για να επαληθεύσετε αμέσως την καταλληλότητά
          σας!
        </p>
      </main>

      <section class="reqs-table">
        <div class="section-title">
          Ελάχιστες Απαιτήσεις Υποβολής Αίτησης Erasmus+
        </div>
        <table>
          <tr>
            <th><b>Κριτήριο</b></th>
            <th><b>Προϋπόθεση</b></th>
          </tr>
          <tr>
            <th><b>Τρέχον Έτος Σπουδών</b></th>
            <th>2ο έτος ή μεγαλύτερο (π.χ., 3ο, 4ο κ.λπ.)</th>
          </tr>
          <tr>
            <th><b>Περασμένα μαθήματα (%)</b></th>
            <th>
              ≥70% των μαθημάτων που έχουν περαστεί μέχρι το τέλος του
              προηγούμενου ακαδημαϊκού έτους
            </th>
          </tr>
          <tr>
            <th><b>Μέσος Όρος Βαθμού</b></th>
            <th>≥6.50 (σε κλίμακα του 10) για όλα τα περασμένα μαθήματα</th>
          </tr>
          <tr>
            <th><b>Πιστοποιητικό Αγγλικής Γλωσσομάθειας</b></th>
            <th>
              Επίπεδο B2 ή υψηλότερο (π.χ., NOCN, Michigan ή πανεπιστημιακή
              πιστοποίηση)
            </th>
          </tr>
        </table>
      </section>

      <section class="check-form">
        <div class="section-title">Ελέγξτε τα Κριτήριά σας σε 1 Λεπτό!</div>
        <div class="form-container">
          <form id="eligibilityForm">
            <label>Τρέχον Έτος Σπουδών:</label>
            <select name="year" id="" required>
              <option value=""></option>
              <option value="1">1ο Έτος</option>
              <option value="2">2ο Έτος</option>
              <option value="3">3ο Έτος</option>
              <option value="4">4ο Έτος</option>
              <option value="5+">5ο Έτος+</option>
            </select>

            <label>Περασμένα μαθήματα (%):</label>
            <input type="number" name="percentage" min="0" max="100" required />

            <label>Μέσος Βαθμός:</label>
            <input
              type="number"
              name="average"
              step="0.01"
              min="0"
              max="10"
              required
            />

            <label>Επίπεδο Αγγλικών</label>
            <div class="radio-group">
              <input
                class="radio-input"
                type="radio"
                name="english"
                value="A1"
                required
              />A1
              <input
                class="radio-input"
                type="radio"
                name="english"
                value="A2"
              />A2
              <input
                class="radio-input"
                type="radio"
                name="english"
                value="B1"
              />B1
              <input
                class="radio-input"
                type="radio"
                name="english"
                value="B2"
              />B2
              <input
                class="radio-input"
                type="radio"
                name="english"
                value="C1"
              />C1
              <input
                class="radio-input"
                type="radio"
                name="english"
                value="C2"
              />C2
            </div>

            <button type="submit">Έλεγχος Καταλληλότητας</button>
          </form>
        </div>

        <div class="form-note">
          Αυτός είναι ένας προκαταρκτικός έλεγχος. Η τελική καταλληλότητά
          επιβεβαιώνεται από το Γραφείο Erasmus μετά την επαλήθευση των
          εγγράφων.
        </div>
      </section>

      <section class="pdf-download">
        <div class="section-title">
          Χρειάζεστε περισσότερες λεπτομέρειες? Κατεβάστε τον οδηγό μας.
        </div>
        <div class="download-icon-container">
          <a
            href="../media/files/download/erasmus-programme-guide-v2.2025_el.pdf"
            class="download-link"
            download="erasmus-programme-guide-v2.2025_el.pdf"
          >
            <img
              class="pdf-icon"
              src="../media/pdf-icon.png"
              alt="pdf icon for download"
            />
            <div class="download-text">Erasmus+ Programme Guide</div>
          </a>
        </div>
      </section>

      <section class="call-to-action">
        <div class="call-to-action-text">
          <br />Έτοιμοι να κάνετε αίτηση; Επισκεφθείτε το Γραφείο Erasmus ή
          <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true):  ?>
            ελέγξτε τη <a href="../html/application.php">Σελίδα Αιτήσεων</a>
          <?php else: ?>
            <a href="login.php">Συνδεθείτε</a>
          <?php endif; ?>
          για να γίνετε μέλος της ομάδας μας!
        </div>
        <img
          class="call-to-action-img"
          src="../media/call-to-action-apply-img.png"
          alt="call to action apply image"
        />
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
