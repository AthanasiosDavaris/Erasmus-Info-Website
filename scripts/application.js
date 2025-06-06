document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('.application-form');

  if (form) {
    form.addEventListener('submit', function(event) {
      event.preventDefault();

      const formData = new FormData(form);
      const submitButton = form.querySelector('button[type="submit"]');

      submitButton.disabled = true;
      submitButton.textContent = 'Γίνεται υποβολή...';

      fetch('submit_application.php', {
        method:'POST',
        body: formData
      })
      .then(response => {
        if (response.ok && response.headers.get("Content-Type")?.includes("application/json")) {
          return response.json();
        }
        window.location.reload();
      })
      .then(data => {
        if (data && data.status === 'success') {
          document.getElementById('confirmMessage').style.display = 'block';
          window.scrollTo({ top: 0, behavior: 'smooth'});
          form.reset();
          submitButton.textContent = 'Υποβολή Αίτησης!!';
          submitButton.disabled = false;
        }
      })
      .catch(error => {
        console.error('An unexpected error occurred:', error);
        window.location.reload();
      });
    });
  }
});