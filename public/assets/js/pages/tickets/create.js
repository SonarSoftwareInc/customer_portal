document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('ticketForm');
    const submitButton = document.getElementById('submitButton');

    form.addEventListener('submit', function () {
        // Disable the submit button to prevent multiple clicks
        submitButton.disabled = true;
    });
});