<!DOCTYPE html>
<html lang="en">
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
        <a href="index.html">
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
            <li><a href="application.html">Δήλωση</a></li>
            <li><a href="sign-up.html">Εγγραφή</a></li>
            <li><a href="login.html">Σύνδεση</a></li>
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
        <form class="application-form">
          <fieldset>
            <legend>Προσωπικά Στοιχεία</legend>

            <div class="form-box">
              <label>Όνομα &#9888;</label>
              <input
                type="text"
                name="firstname"
                required
                placeholder="Π.χ. Αθανάσιος"
              />
            </div>

            <div class="form-box">
              <label>Επίθετο &#9888;</label>
              <input
                type="text"
                name="lastname"
                required
                placeholder="Π.χ. Ντάβαρης"
              />
            </div>

            <div class="form-box">
              <label>Αριθμός Μητρώου &#9888;</label>
              <input type="text" name="dit_uop_id" required pattern="[0-9]{13}
              placeholder="Π.χ. 2022202200150">
            </div>
          </fieldset>

          <fieldset>
            <legend>Ακαδημαϊκά Στοιχεία</legend>

            <div class="form-box">
              <label>Ποσοστό περασμένων μαθημάτων (%) &#9888;</label>
              <input
                type="number"
                name="percentage_passed_lessons"
                min="70"
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
                min="6.5"
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
                <label
                  ><input type="radio" name="english" value="B2" required />
                  B2</label
                >
                <label
                  ><input type="radio" name="english" value="C1" /> C1</label
                >
                <label
                  ><input type="radio" name="english" value="C2" /> C2</label
                >
              </div>
            </div>

            <div class="form-box">
              <label>Γνώση άλλων γλωσσών; &#9888;</label>
              <div class="radio-group">
                <label
                  ><input type="radio" name="other_languages" value="yes" />
                  Ναι</label
                >
                <label
                  ><input type="radio" name="other_languages" value="no" />
                  Όχι</label
                >
              </div>
            </div>
          </fieldset>

          <fieldset>
            <legend>Προτιμήσεις Πανεπιστημίων</legend>

            <div class="form-box">
              <label>1η Επιλογή &#9888;</label>
              <select name="university1" required>
                <option value=""></option>
                <option>University of Oxford</option>
                <option>Imperial College London</option>
                <option>Alexandria University</option>
                <option>University of Barcelona</option>
                <option>Humboldt University</option>
              </select>
            </div>

            <div class="form-box">
              <label>2η Επιλογή &#9888;</label>
              <select name="university2" required>
                <option value=""></option>
                <option>University of Oxford</option>
                <option>Imperial College London</option>
                <option>Alexandria University</option>
                <option>University of Barcelona</option>
                <option>Humboldt University</option>
              </select>
            </div>

            <div class="form-box">
              <label>3η Επιλογή &#9888;</label>
              <select name="university3" required>
                <option value=""></option>
                <option>University of Oxford</option>
                <option>Imperial College London</option>
                <option>Alexandria University</option>
                <option>University of Barcelona</option>
                <option>Humboldt University</option>
              </select>
            </div>
          </fieldset>

          <fieldset>
            <legend>Επίσημα Έγγραφα</legend>

            <div class="form-box">
              <label>Αναλυτική Βαθμολογία (PDF) &#9888;</label>
              <input
                type="file"
                accept=".doc, .docx, .rtf, .pdf, .wpd"
                required
              />
            </div>

            <div class="form-box">
              <label>Πτυχίο Αγγλικών</label>
              <input
                type="file"
                accept=".doc, .docx, .rtf, .pdf, .wpd, .jpg, .png"
              />
            </div>

            <div class="form-box">
              <label>Πτυχία άλλων ξένων γλωσσών</label>
              <input
                type="file"
                accept=".doc, .docx, .rtf, .pdf, .wpd, .jpg, .png"
                multiple
              />
            </div>
          </fieldset>

          <div class="terms-box">
            <label>
              <input type="checkbox" required />
              Αποδέχομαι τους
              <a href="terms.html" target="_blank" rel="noopener noreferrer"
                >Όρους Συμμετοχής</a
              >
            </label>
          </div>

          <button type="submit" onclick="showConfirmMessage()">
            Υποβολή Αίτησης!!
          </button>
        </form>
      </section>
    </div>

    <div class="confirm-message" id="confirmMessage">
      <div class="confirm-content">
        <div class="confirm-text">
          &#10003; Η αίτησή σας υποβλήθηκε με επιτυχία!
        </div>

        <button
          class="return-button"
          onclick="window.location.href='index.html'"
        >
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
