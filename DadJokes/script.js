document.addEventListener('DOMContentLoaded', function() {
    let isFetching = false;

    document.getElementById('dadjokeg_new-joke-btn').addEventListener('click', function() {
        if (isFetching) return;
        isFetching = true;
        fetch(dadJokesAjax.ajax_url, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=dadjokeg_fetch_joke' // Ensure this matches the action name in PHP
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('dadjokeg_setup').textContent = data.data.setup;
                document.getElementById('dadjokeg_punchline').textContent = data.data.punchline;
            } else {
                displayError('Failed to fetch a new joke. Please try again later.');
            }
        })
        .catch(error => {
            console.error('Error fetching joke:', error);
            displayError('An error occurred while fetching a new joke.');
        })
        .finally(() => {
            isFetching = false;
        });
    });

    function displayError(message) {
        const jokeContainer = document.getElementById('dadjokeg_joke-container');
        jokeContainer.innerHTML = ''; // Clear previous content/error messages
        const errorElement = document.createElement('p');
        errorElement.textContent = message;
        errorElement.style.color = 'red';
        jokeContainer.appendChild(errorElement);
    }

    // Trigger a joke load on initial page load
    document.getElementById('dadjokeg_new-joke-btn').click();
});