'use strict';
console.log("JavaScript working.");


// Function handles opening the profile form.
function openForm() {

    // Grab the body and the form.
    const body = document.querySelector('#body');
    const form = document.querySelector('#profile-form');

    // Adjust the interactivity and styling of the background
    // and display the form.
    body.style.opacity = "50%";
    body.style.pointerEvents = "none";
    form.style.display = "block";

    // Add functionality to close the form when Escape is pressed.
    document.addEventListener("keydown", function formEscape(e) {

        if (e.key === "Escape") {

            // If Escape is pressed, close the form and
            // remove the event listener.
            closeForm();
            document.removeEventListener("keydown", formEscape);
        }
    });
}


// Function handles closing the profile form.
function closeForm() {

    // Grab the body and the form.
    const body = document.querySelector('#body');
    const form = document.querySelector('#profile-form');

    // Adjust the interactivity and styling of the background
    // and display the form.
    body.style.opacity = "100%";
    body.style.pointerEvents = "all";
    form.style.display = "none";
}
