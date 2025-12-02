// Confirm user deletion with a dialog prompt
function confirmDelete() {
    return confirm("Are you sure you want to delete this user?");
}

// Confirm post deletion with a dialog prompt
function confirmDeletePost() {
    return confirm("Are you sure you want to delete this post?");
}



document.addEventListener('DOMContentLoaded', () => {
    const dropdownButton = document.querySelector('.dropdown > button');
    const dropdownMenu = document.querySelector('.dropdown-menu');

    // Toggle dropdown menu visibility on button click
    dropdownButton.addEventListener('click', () => {
        dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
    });

    // Close dropdown when clicking outside of it
    document.addEventListener('click', (e) => {
        if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
            dropdownMenu.style.display = 'none';
        }
    });
});