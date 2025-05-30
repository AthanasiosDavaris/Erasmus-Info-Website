document.addEventListener('DOMContentLoaded', function(){
  const eligibilityForm = document.getElementById('eligibilityForm');

  let result = document.getElementById('EligibilityResult');
  if (!result) {
    result = document.createElement('div');
    result.id = 'EligibilityResult';
    result.style.marginTop = '20px';
    result.style.marginLeft = '10px';
    const formContainer = document.querySelector('.form-container');
    if (formContainer) {
      formContainer.appendChild(result);
    } else {
      console.error('Form container not found. Result may not be displayed as intended.');
      if (eligibilityForm) {
        eligibilityForm.parentNode.insertBefore(result, eligibilityForm.nextSibling);
      }
    }
  }

  if(eligibilityForm) {
    eligibilityForm.addEventListener('submit', function(event) {
      event.preventDefault();

      const year = document.querySelector('select[name="year"]').value;
      const passedCourses = document.querySelector('input[name="percentage"]').value;
      const averageGrade = parseFloat(document.querySelector('input[name="average"]').value);
      const englishLevel = document.querySelector('input[name="english"]:checked').value;

      let messages = [];
      let eligibility = true;

      const eligibleYears = ['2', '3', '4', '5+'];
      if (year && eligibleYears.includes(year)) {
        messages.push('<strong>Τρέχον Έτος Σπουδών:</strong> <span style="color: green;">Πληροίται</span> (Επιλεγμένο: ' + year + ')');
      } else {
        messages.push('<strong>Τρέχον Έτος Σπουδών:</strong> <span style="color: red;">Δεν πληροίται</span> (Απαιτείται: 2ο έτος ή μεγαλύτερο, Επιλεγμένο: ' + year + ')');
        eligibility = false;
      }

      if (!isNaN(passedCourses) && passedCourses >= 70 && passedCourses <= 100) {
        messages.push('<strong>Περασμένα μαθήματα (%):</strong> <span style="color: green;">Πληροίται</span> (Εισαγωγή: ' + passedCourses + '%)');
      } else {
        let reason = '';
        if (isNaN(passedCourses)) {
          reason = 'Μη έγκυρη τιμή';
        } else if (passedCourses <0 || passedCourses > 100) {
          reason = 'Η τιμή πρέπει να είναι μεταξύ 0 και 100';
        } else {
          reason = 'Απαιτείται: ≥70%';
        }
        messages.push('<strong>Περασμένα μαθήματα (%):</strong> <span style="color: red;">Δεν πληροίται</span> (' + reason + ', Εισαγωγή: ' + (passedCourses.value || 'Καμία') + '%)');
        eligibility = false;
      }

      if(!isNaN(averageGrade) && averageGrade >= 6.50 && averageGrade <= 10) {
        messages.push('<strong>Μέσος Όρος Βαθμού:</strong> <span style="color: green;">Πληροίται</span> (Εισαγωγή: ' + averageGrade.toFixed(2) + ')')
      } else {
        let reason = '';
        if (isNaN(averageGrade)) {
          reason = 'Μη έγκυρη τιμή';
        } else if (averageGrade < 0 || averageGrade > 10) {
          reason = 'Η τιμή πρέπει να είναι μεταξύ 0.00 και 10.00';
        } else {
          reason = 'Απαιτείται: ≥6.50';
        }
        messages.push('<strong>Μέσος Όρος Βαθμού:</strong> <span style="color: red;">Δεν πληροίται</span> (' + reason + ', Εισαγωγή: ' + (document.querySelector('input[name="average"]').value || 'Καμία') + ')')
        eligibility = false;
      }

      const eligibleEnglishLevels = ['B2', 'C1', 'C2'];
      if (englishLevel && eligibleEnglishLevels.includes(englishLevel)) {
        messages.push('<strong>Πιστοποιητικό Αγγλικής Γλωσσομάθειας:</strong> <span style="color: green;">Πληροίται</span> (Επιλεγμένο: ' + englishLevel + ')');
      } else {
        messages.push('<strong>Πιστοποιητικό Αγγλικής Γλωσσομάθειας:</strong> <span style="color: red;">Δεν πληροίται</span> (Απαιτείται: B2 ή υψηλότερο, Επιλεγμένο: ' + (englishLevel || 'Καμία επιλογή') + ')');
        eligibility = false;
      }

      let resultHTML = '<h4>Αποτελέσματα Ελέγχου Καταλληλότητας:</h4><ul>';
      messages.forEach(function(message) {
        resultHTML += '<li>' + message + '</li>';
      });
      resultHTML += '</ul>';

      if (eligibility) {
        resultHTML += '<p style="color: green; font-weight: bold;">Συνολικά: Είστε προκαταρκτικά επιλέξιμος/η για το πρόγραμμα Erasmus+ βάσει των κριτηρίων που δηλώσατε.</p>';
      } else {
        resultHTML += '<p style="color: red; font-weight: bold;">Συνολικά: Δεν είστε προκαταρκτικά επιλέξιμος/η για το πρόγραμμα Erasmus+ βάσει ενός ή περισσοτέρων κριτηρίων που δηλώσατε.</p>';
      }

      const formNote = document.querySelector('.form-note');
      if (formNote) {
        resultHTML += '<p style="font-size: 0.9em; margin-top:15px;"><em>' + formNote.textContent + '</em></p>';
      }

      result.innerHTML = resultHTML;

    })
  } else {
    console.error('Form with ID "eligibilityForm" not found.');
  }
});