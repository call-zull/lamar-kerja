const darkModeToggle = document.getElementById('dark-mode-toggle');
    darkModeToggle.addEventListener('click', function () {
        document.body.classList.add('dark-mode');
        document.body.classList.remove('light-mode');
    });

    // Light Mode Toggle
    const lightModeToggle = document.getElementById('light-mode-toggle');
    lightModeToggle.addEventListener('click', function () {
        document.body.classList.remove('dark-mode');
        document.body.classList.add('light-mode');
    });