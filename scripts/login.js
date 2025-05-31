document.addEventListener('DOMContentLoaded', function() {
  const params = new URLSearchParams(window.location.search);
  const messageArea = document.getElementById('messageArea');
  let messageText = '';
  let messageClass = '';

  if (params.has('error')) {
    const errorCode = params.get('error');
    if (errorCode === '1') {
      messageText = 'Λάθος όνομα χρήστη ή κωδικός πρόσβασης.';
      messageClass = 'error-message';
    } else if (errorCode === '2') {
      messageText = 'Το όνομα χρήστη και ο κωδικός πρόσβασης είναι υποχρεωτικά.';
      messageClass = 'error-message';
    } else if (errorCode === '3') {
      messageText = 'Προέκυψε ένα σφάλμα. Παρακαλώ προσπαθήστε ξανά.';
      messageClass = 'error-message';
    }
  } else if (params.has('signup') && params.get('signup') === 'success') {
    messageText = 'Η εγγραφή ολοκληρώθηκε με επιτυχία! Μπορείτε τώρα να συνδεθείτε.';
    messageClass = 'success-message';
  } else if (params.has('logged_out') && params.get('logged_out') === 'true') {
    messageText = 'Έχετε αποσυνδεθεί με επιτυχία.';
    messageClass = 'success-message';
  }

  if (messageText) {
    messageArea.innerHTML = `<div class="${messageClass}">${messageText}</div>`;
    messageArea.className = messageClass;
    messageArea.style.display = 'block';
    messageArea.textContent = messageText;

    if (history.replaceState) {
      const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
      history.replaceState({ path: cleanUrl}, '', cleanUrl);
    }
  }
});