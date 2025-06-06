document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('profileUpdateForm');
  if (form) {
    form.addEventListener('submit', function(event) {
      const changedFieldsMessages = [];
      const fieldDefs = [
        { id: 'firstName', label: 'Όνομα'},
        { id: 'lastName', label: 'Επίθετο' },
        { id: 'studentId', label: 'Αριθμός Μητρώου' },
        { id: 'phone', label: 'Τηλέφωνο' },
        { id: 'email', label: 'Email' }
      ];

      let haveChanges = false;

      fieldDefs.forEach(field => {
        const input = document.getElementById(field.id);
        if (input) {
          const currentVal = input.ariaValueMax.trim();
          const originalVal = input.dataset.originalVal.trim();

          if(currentVal !== originalVal) {
            if (field.isRequired && currentVal === '') {
              changedFieldsMessages.push(field.label + ": ΠΡΟΣΟΧΗ! Το πεδίο είναι υποχρεωτικό και δεν μπορεί να είναι κενό.");
              haveChanges = true;
            } else if (currentVal !== '') {
              changedFieldsMessages.push(field.label + ": από '" + originalVal + "' σε '" + currentVal + "'");
              haveChanges = true;
            }
          }
        }
      });

      const newPasswordInput = document.getElementById('newPassword');
      if (newPasswordInput && newPasswordInput.value !== '') {
        const confirmPasswordInput = document.getElementById('confirmNewPassword');
        if (newPasswordInput.value.length < 5) {
          alert("Ο νέος κωδικός πρέπει να περιέχει τουλάχιστον έναν ειδικό χαρακτήρα.");
          event.preventDefault();
          return;
        }
        if (newPasswordInput.value !== confirmPasswordInput.value) {
          alert("Οι νέοι κωδικοί πρόσβασης δεν ταιριάζουν.");
          event.preventDefault();
          return;
        }
        changedFieldsMessages.push('Κωδικός Πρόσβασης: θα αλλάξει');
        haveChanges = true;
      }

      if (changedFieldsMessages.length > 0) {
        let confirmMessage = "Επισκόπηση αλλαγών:\n\n- " + changedFieldsMessages.join("\n- ");

        if (!haveChanges) {
          alert("Δεν εντοπίστηκαν αλλαγές για αποθήκευση.");
          event.preventDefault();
          return;
        }

        confirmMessage += "\n\nΕίστε σίγουροι ότι θέλετε να συνεχίσετε;";

        if(!window.confirm(confirmMessage)) {
          event.preventDefault();
        } 
      } else {
        alert("Δεν εντοπίστηκαν αλλαγές για αποθήκευση.");
        event.preventDefault();
      }
    });
  }
});