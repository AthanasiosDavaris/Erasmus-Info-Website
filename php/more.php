<?php
  session_start();
  require_once 'db_config.php';

  $accepted_students = [];

  try {
    $db_info = "mysql:host=$db_host;dbname=$db_name;charset=utf8";
    $pdo = new PDO($db_info, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);

    $settings = $pdo->query("SELECT results_published FROM application_settings WHERE id = 1")->fetch();

    if ($settings && $settings['results_published'] === 'yes') {
      $accepted_students = $pdo->query("SELECT first_name, last_name, university1 FROM applications WHERE status = 'accepted' ORDER BY last_name ASC")->fetchAll();
    }
  } catch (PDOException $e) {
    error_log("Error on more.php: " . $e->getMessage());
  }
?>
<!DOCTYPE html>
<html lang="el">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../styles/header.css" />
    <link rel="stylesheet" href="../styles/more.css" />
    <link rel="stylesheet" href="../styles/footer.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet"
    />
    <script src="../scripts/header.js"></script>
    <title>ErasmApp Info</title>
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
        <div class="main-title">
          Περισσότερες Πληροφορίες για το Πρόγραμμα Erasmus+
        </div>
        <div class="main-text">
          Εξερευνήστε πέρα από τα βασικά! Αυτή η σελίδα είναι η πύλη σας για
          βαθύτερες γνώσεις, εμπειρίες από πρώτο χέρι και πρακτικά εργαλεία για
          τον προγραμματισμό του ταξιδιού σας στο Erasmus. Βυθιστείτε στις
          λεπτομέρειες του πανεπιστημίου, τις ιστορίες των φοιτητών και τους
          βασικούς οδηγούς για να κάνετε την περιπέτειά σας αξέχαστη.
        </div>
      </main>

      <?php if (!empty($accepted_students)): ?>
        <section class="results-section">
          <div class="section-title">Αποτελέσματα Erasmus+</div>
          <p>Συγχαρητήρια στους παρακάτω φοιτητές που επιλέχθηκαν για το πρόγραμμα Erasmus+!</p>
          <div class="table-responsive">
            <table class="results-table">
              <thead>
                <tr>
                  <th>Επίθετο</th>
                  <th>Όνομα</th>
                  <th>Πανεπιστήμιο 1ης Επιλογής</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($accepted_students as $student): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['university1']); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>
      <?php endif; ?>

      <section class="part-unis">
        <div class="section-title">Συνεργαζόμενα Πανεπιστήμια</div>
        <div class="part-unis-text">
          Ανακαλύψτε το παγκόσμιο δίκτυο συνεργαζόμενων ιδρυμάτων μας. Κάθε
          πανεπιστήμιο προσφέρει μοναδικά ακαδημαϊκά προγράμματα, πολιτιστικές
          εμπειρίες και υποστήριξη για φοιτητές Erasmus:
        </div>
        <div class="part-unis-list">
          <ul>
            <li class="oxford">
              <div class="uni-name">
                University of Oxford - Ηνωμένο Βασίλειο
              </div>
              <div class="uni-info">
                <b>Ακαδημαϊκά πλεονεκτήματα:</b> Ανθρωπιστικές Επιστήμες,
                Κοινωνικές Επιστήμες, Ιατρική, Νομική. <br />
                <b>Αξιοθέατα της πόλης:</b> Ιστορικά κολέγια, βιβλιοθήκη
                Bodleian, ζωντανές φοιτητικές κοινωνίες. <br />
                <a href="https://www.ox.ac.uk/about/international-oxford"
                  >Σελίδα Erasmus+ του πανεπιστημίου</a
                >
                |
                <a href="https://youtu.be/gb7hzkzr-4Y?si=AHw0mFyfChIsu2yg"
                  >Περιήγηση(Youtube)</a
                >
              </div>
            </li>
            <li class="london">
              <div class="uni-name">
                Imperial College London - Ηνωμένο Βασίλειο
              </div>
              <div class="uni-info">
                <b>Ακαδημαϊκά πλεονεκτήματα:</b> Μηχανική, STEM, Επιχειρηματική
                Καινοτομία. <br />
                <b>Αξιοθέατα της πόλης:</b> Κέντρο της πόλης του Λονδίνου,
                μουσεία, πολυπολιτισμικοί κόμβοι. <br />
                <a href="https://www.imperial.ac.uk/about/global/"
                  >Σελίδα Imperial Global του πανεπιστημίου</a
                >
                |
                <a href="https://youtu.be/ByoAFVZ6fjI?si=pJT51gvBnNV9lJ2B"
                  >Βίντεο περιήγησης στην πανεπιστημιούπολη</a
                >
              </div>
            </li>
            <li class="alexandria">
              <div class="uni-name">Alexandria University - Αίγυπτος</div>
              <div class="uni-info">
                <b>Ακαδημαϊκά πλεονεκτήματα:</b> Θαλάσσιες Επιστήμες, ηθοποιία,
                Αρχαιολογία. <br />
                <b>Αξιοθέατα της πόλης:</b> Μεσογειακή ακτογραμμή, Βιβλιοθήκη
                της Αλεξάνδρειας, αρχαία ερείπια. <br />
                <a
                  href="https://www.alexu.edu.eg/index.php/en/university-scholarships/3558-application-is-now-available-for-erasmus-research-projects-programme"
                  >Διεθνές Γραφείο Πανεπιστημίου</a
                >
                |
                <a href="https://www.youtube.com/watch?v=aPoF4uSKPt0"
                  >Εξερευνήστε την Αλεξάνδρεια</a
                >
              </div>
            </li>
            <li class="barcelona">
              <div class="uni-name">University of Barcelona - Ισπανία</div>
              <div class="uni-info">
                <b>Ακαδημαϊκά πλεονεκτήματα:</b> Επιχειρήσεις, Μεσογειακές
                Σπουδές. <br />
                <b>Αξιοθέατα της πόλης:</b> Παραλίες, αρχιτεκτονική Gaudí.
                <br />
                <a
                  href="https://www.ub.edu/economiaempresa-internacional/web/en/international-exchange-students/"
                  >Σελίδα Erasmus+ του πανεπιστημίου</a
                >
                <a href="https://www.youtube.com/watch?v=jw8ucsL8OFo"
                  >Περιήγηση(Youtube)</a
                >
              </div>
            </li>
            <li class="humboldt">
              <div class="uni-name">Humboldt University - Γερμανία</div>
              <div class="uni-info">
                <b>Ακαδημαϊκά πλεονεκτήματα:</b> Φιλοσοφία, Μηχανική<br />
                <b>Αξιοθέατα της πόλης:</b> Μνημεία του Βερολίνου, Έντονη
                νυχτερινή ζωή.<br />
                <a
                  href="https://www.international.hu-berlin.de/en/going-abroad/programmes/erasmus"
                  >Σελίδα Erasmus+ του πανεπιστημίου</a
                >
                <a href="https://www.youtube.com/watch?v=yAU87wa5PkA"
                  >Περιήγηση(Youtube)</a
                >
              </div>
            </li>
          </ul>
        </div>

        <aside class="part-unis-media">
          <div class="oxford-img">
            <img src="../media/uni-oxford-img.jpg" alt="University of Oxford" />
          </div>
          <div class="london-img">
            <img
              src="../media/uni-london-img.jpg"
              alt="Imperial College of London"
            />
          </div>
          <div class="alexandria-img">
            <img
              src="../media/uni-alex-img.jpg"
              alt="Alexandria Univrsity Image"
            />
          </div>
          <div class="barcelona-img">
            <img
              src="../media/uni-barc-img.jpg"
              alt="University of Barcelona Image"
            />
          </div>
          <div class="humboldt-img">
            <img
              src="../media/uni-Humboldt-img.jpg"
              alt="Humboldt University Image"
            />
          </div>
        </aside>
      </section>

      <section class="student-interviews">
        <div class="section-title">Εμπειρίες Φοιτητών & Συνεντεύξεις</div>

        <div class="student-interviews-maintext">
          Ακούστε από εκείνους που το έχουν ζήσει!
        </div>

        <div class="interview-container">
          <div class="student-name">Daniel, MSc Tropical Forestry</div>
          <div class="student-words">
            "be ready to jump into a whole new world with many exciting Erasmus
            experiences."
          </div>
          <div class="interview-link">
            <a
              href="https://blog.erasmusplay.com/experience-studying-in-europe/"
            >
              Διαβάστε ολόκληρη τη συνέντευξη
            </a>
          </div>
        </div>

        <div class="interview-container">
          <div class="student-name">
            Stephanie St., Cyprus University of Technology
          </div>
          <div class="student-words">
            "Αν μου δινόταν η ευκαιρία θα συμμετείχα ξανά στο πρόγραμμα
            Erasmus+"
          </div>
          <div class="interview-link">
            <a
              href="https://erasmus.panteion.gr/en/erasmus-for-students/eiserxomenoi-foitites-evropi/success-stories/stephane-st-stephanie-st-cyprus-university-of-technology-cyprus"
            >
              Διαβάστε ολόκληρη τη συνέντευξη
            </a>
          </div>
        </div>

        <div class="interview-container">
          <div class="student-name">Alexia</div>
          <div class="student-words">
            "Να μην σταματήσετε ποτέ να κάνετε όνειρα"
          </div>
          <div class="interview-audio">
            <audio controls>
              <source
                src="../media/videos & audio/QnA_-ERASMUS-__-Απαντώ-στις-ερωτήσεις-σας.mp3"
                type="audio/mpeg"
              />
            </audio>
          </div>
        </div>
      </section>

      <section class="faq">
        <div class="section-title">Συχνές Ερωτήσεις (FAQs)</div>
        <div class="faq-container">
          <div class="question">
            <b>Q: </b>Μπορώ να παρατείνω τη διαμονή μου στο Erasmus;
          </div>
          <div class="answer">
            <b>A: </b>Ναι! Συζητήστε με τον συντονιστή σας 2 μήνες πριν από τη
            λήξη της διαμονής σας.
          </div>
        </div>

        <div class="faq-container">
          <div class="question">
            <b>Q: </b>Τι γίνεται αν δεν μιλάω την τοπική γλώσσα;
          </div>
          <div class="answer">
            <b>A: </b>Τα περισσότερα πανεπιστήμια του προγράμματος προσφέρουν
            μαθήματα στα αγγλικά. Δοκιμάστε το
            <a href="https://www.duolingo.com/">Duolingo</a> για να μάθετε τα
            βασικά!
          </div>
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
