// Track whether any form on the page has unsaved changes
window.isFormDirty = false;

// Handle cancel button click with unsaved changes warning
function handleCancel(event) {
    if (window.isFormDirty) {
        const confirmation = confirm("You have unsaved changes. Are you sure you want to leave?");
        if (!confirmation) {
            event.preventDefault();
            return;
        }
    }
    window.location.href = '/blogtech/views/profile/profile';
}

window.addEventListener("DOMContentLoaded", function () {
    const forms = document.querySelectorAll("form");

    forms.forEach(form => {
        // Mark form as dirty when user makes any changes
        form.addEventListener("input", () => {
            window.isFormDirty = true;
        });

        // Clear dirty flag when form is submitted
        form.addEventListener("submit", () => {
            window.isFormDirty = false;
        });
    });

    // Warn user before leaving page with unsaved changes
    window.addEventListener("beforeunload", (e) => {
        if (window.isFormDirty) {
            e.preventDefault();
            e.returnValue = "You have unsaved changes. Are you sure you want to leave?";
        }
    });
});