document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('sign-up-form');
  const firstName = document.getElementById('firstName');
  const lastName = document.getElementById('lastName');
  const studentId = document.getElementById('studentId');
  const phone = document.getElementById('phone');
  const email = document.getElementById('email');
  const username = document.getElementById('username');
  const password = document.getElementById('password');
  const confirmPassword = document.getElementById('confirmPassword');
  const terms = document.getElementById('terms');

  const firstNameError = document.getElementById('firstNameError');
  const lastNameError = document.getElementById('lastNameError');
  const studentIdError = document.getElementById('studentIdError');
  const phoneError = document.getElementById('phoneError');
  const emailError = document.getElementById('emailError');
  const usernameError = document.getElementById('usernameError');
  const passwordError = document.getElementById('passwordError');
  const confirmPasswordError = document.getElementById('confirmPasswordError');
  const termsError = document.getElementById('termsError');
  const formMainError = document.getElementById('formMainError');

  form.addEventListener('submit', function (event) {
    let isValid = true;
    formMainError.textContent = '';

    clearErrors();

    if (firstName.value.trim() === '') {
      displayError(lastNameError, 'Το όνομα είναι υποχρεωτικό.');
      isValid = false;
    } else if (/[0-9]/.test(firstName.value)) {
      displayError(lastNameError, 'Το όνομα δεν πρέπει να περιέχει ψηφία.');
      isValid = false;
    }

    if (lastName.value.trim() === '') {
      displayError(lastNameError, 'Το επώνυμο είναι υποχρεωτικό.');
      isValid = false;
    } else if (/[0-9]/.test(lastName.value)) {
      displayError(lastNameError, 'Το επώνυμο δεν πρέπει να περιέχει ψηφία.');
      isValid = false;
    }

    if (studentId.value.trim() === '') {
      displayError(studentIdError, 'Ο αριθμός μητρώου είναι υποχρεωτικός.');
      isValid = false;
    } else if (!/^2022[0-9]{9}$/.test(studentId.value)) {
      displayError(studentIdError, 'Ο αριθμός μητρώου πρέπει να είναι 13 ψηφία και να ξεκινά με "2022".');
      isValid = false;
    }

    if (phone.value.trim() === '') {
      displayError(phoneError, 'Το τηλέφωνο είναι υποχρεωτικό.');
      isValid = false;
    } else if (!/^[0-9]{10}$/.test(phone.value)) {
      displayError(phoneError, 'Το τηλέφωνο πρέπει να αποτελείται από ακριβώς 10 ψηφία.');
      isValid = false;
    }

    if (email.value.trim() === '') {
      displayError(emailError, 'Η διεύθυνση mail είναι υποχρεωτική.');
      isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
      displayError(emailError, 'Η διεύθυνση mail δεν είναι έγκυρη.');
      isValid = false;
    }

    if (username.value.trim() === '') {
      displayError(usernameError, 'Το όνομα χρήστη είναι υποχρεωτικό.');
      isValid = false;
    }

    const passwordValue = password.value;
    if (passwordValue === '') {
      displayError(passwordError, 'Ο κωδικός πρόσβασης είναι υποχρεωτικός.');
      isValid = false;
    } else if (passwordValue.length < 5) {
      displayError(passwordError, 'Ο κωδικός πρόσβασης πρέπει να έχει τουλάχιστον 5 χαρακτήρες.');
      isValid = false;
    } else if (!/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/.test(passwordValue)) {
      displayError(passwordError, 'Ο κωδικός πρόσβασης πρέπει να περιέχει τουλάχιστον έναν ειδικό χαρακτήρα.');
      isValid = false;
    }

    if (confirmPassword.value == '') {
      displayError(confirmPasswordError, 'Η επιβεβαίωση κωδικού είναι υποχρεωτική.');
      isValid = false;
    } else if (confirmPassword.value !== passwordValue) {
      displayError(confirmPasswordError, 'Οι κωδικοί πρόσβασης δεν ταιριάζουν.');
      isValid = false;
    }

    if (!terms.checked) {
      displayError(termsError, 'Πρέπει να αποδεχτείτε τους όρους.');
      isValid = false;
    }

    if (!isValid) {
      event.preventDefault();
      formMainError.textContent = 'Παρακαλώ διορθώστε τα σφάλματα στη φόρμα.';
    }
  });

  function displayError(element, message) {
    element.textContent = message;
    element.previousElementSibling.classList.add('error-input');
  }

  function clearErrors() {
    firstNameError.textContent = '';
    lastNameError.textContent = '';
    studentIdError.textContent = '';
    phoneError.textContent = '';
    emailError.textContent = '';
    usernameError.textContent = '';
    passwordError.textContent = '';
    confirmPasswordError.textContent = '';
    termsError.textContent = '';
    formMainError.textContent = '';

    const inputs = form.querySelectorAll('input');
    inputs.forEach(input => input.classList.remove('error-input'));
  }

  form.addEventListener('reset', clearErrors);
});