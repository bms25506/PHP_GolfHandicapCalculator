document.addEventListener('DOMContentLoaded', function () {
   

    // Mobile Main Navigation Toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-nav ul');

    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function () {
            mainNav.classList.toggle('show');
        });
    }

    // Dynamic Score Inputs Based on Number of Holes
    const courseDropdown = document.getElementById('course_id');
    const scoreColumns = document.querySelectorAll('.score-column');

    if (courseDropdown && scoreColumns.length > 0) {
        courseDropdown.addEventListener('change', function () {
            const selectedOption = courseDropdown.options[courseDropdown.selectedIndex];
            const selectedHoles = parseInt(selectedOption.getAttribute('data-holes'), 10);

            scoreColumns.forEach((column, index) => {
                const input = column.querySelector('input');
                if (index < selectedHoles) {
                    column.style.display = 'block';
                    input.disabled = false; // Enable the visible inputs
                } else {
                    column.style.display = 'none';
                    input.disabled = true; // Disable the hidden inputs
                }
            });
        });

        // Trigger change event on page load to display the correct number of inputs
        courseDropdown.dispatchEvent(new Event('change'));
    }
});

