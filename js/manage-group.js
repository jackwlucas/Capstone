'use strict';
console.log("JavaScript working.");


function openForm(form) {

    // Get the body regardless.
    let body = document.getElementById("body");

    // If the function is called with the "S" argument.
    if (form === "S") {

        // Grab the settings form.
        let settings = document.getElementById("manage-group-settings-form");

        // If the form is not displayed.
        if (settings.style.display === "none") {

            // Set the display to block.
            settings.style.display = "block";
        }
    }

    // If the function is called with the "D" argument.
    if (form === "D") {

        // Grab delete form.
        let del = document.getElementById("manage-group-delete-form");

        // If the form is not displayed.
        if (del.style.display === "none") {

            // Set the display to block.
            del.style.display = "block";
        }
    }

    if (form === "L") {

        // Grab leave form.
        let leave = document.getElementById("manage-group-leave-form");

        // If the form is not displayed.
        if (leave.style.display === "none") {

            // Set the display to block.
            leave.style.display = "block";
        }
    }

    // Set the opacity of the main body to 50%
    // and turn off pointer events.
    body.style.opacity = "50%";
    body.style.pointerEvents = "none";
}


function closeForm(form) {

    // Get the body regardless.
    let body = document.getElementById("body");

    // If the function is called with the "S" argument.
    if (form === "S") {

        // Grab the settings form.
        let settings = document.getElementById("manage-group-settings-form");

        // If the form is not displayed.
        if (settings.style.display === "block") {

            // Set the display to block.
            settings.style.display = "none";
        }
    }

    // If the function is called with the "D" argument.
    if (form === "D") {

        // Grab delete form.
        let settings = document.getElementById("manage-group-delete-form");

        // If the form is not displayed.
        if (settings.style.display === "block") {

            // Set the display to block.
            settings.style.display = "none";
        }
    }

    // If the function is called with the "L" argument.
    if (form === "L") {

        // Grab leave form.
        let leave = document.getElementById("manage-group-leave-form");

        // If the form is not displayed.
        if (leave.style.display === "block") {

            // Set the display to block.
            leave.style.display = "none";
        }
    }

    // Set the opacity of the main body to 50%
    // and turn off pointer events.
    body.style.opacity = "100%";
    body.style.pointerEvents = "all";
}


let checkUsers = document.getElementsByClassName("check-user");

for (let i = 0; i < checkUsers.length; i++) {

    let children = checkUsers[i].children;

    children[0].addEventListener("click", () => {

        if (children[0].checked) {
            children[1].style.color = "crimson";
        }

        else {
            children[1].style.color = "black";
        }

    })
}


let search = document.getElementById("manage-group-members-search");

search.addEventListener("keyup", (e) => {

    let input = document.getElementById("manage-group-members-search").value;

    for (let i = 0; i < checkUsers.length; i++) {

        if (!checkUsers[i].innerText.includes(input)) {

            checkUsers[i].parentElement.style.display = "none";
        }

        else {
            checkUsers[i].parentElement.style.display = "";
        }
    }

});


function settingsHover() {

    // Grab the SVG in the button and extracts its paths.
    let svg = document.getElementById("manage-group-settings").children[0];
    let paths = [svg.children[0], svg.children[1]];

    // If the user is hovered over and leaves the button,
    // reset the colors to their original.
    if (paths[0].style.fill === "crimson") {

        paths[0].style.fill = "white";
        paths[1].style.fill = "white";
        paths[0].style.stroke = "black";
        paths[1].style.stroke = "black";
    }

    // Else color the fill and stroke of the SVG.
    else {
        paths[0].style.fill = "crimson";
        paths[1].style.fill = "crimson";
        paths[0].style.stroke = "white";
        paths[1].style.stroke = "white";
    }
}
