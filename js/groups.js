'use strict';
console.log("JavaScript working.");


// Function adds the form to the page.
function openForm () {

    // Log the button click.
    console.log("Open Form clicked.");

    // Grab the body and form elements.
    let body = document.getElementById("body");
    let form = document.getElementById("create-group-form");

    // Insert the form into the page.
    form.innerHTML = `<form method="POST" action="php/form-handlers.php" class="make-group-form" id="make-group-form">
                        <div id="close-form" onclick="closeForm('C')">X</div>
                    
                        <h2>Create Group</h2>
                    
                        <label for="group-name">Name</label>
                        <input class="group-name" name="group-name" id="group-name" type="text" required>
                    
                        <label for="group-desc">Description</label>
                        <textarea class="group-desc" name="group-desc" id="group-desc" maxlength="256" rows="6" cols="20"></textarea>
                    
                        <input type="submit" class="group-submit" name="group-submit" id="group-submit">
                      </form>`;

    // Set the opacity of the main body to 50%
    // and turn off pointer events.
    body.style.opacity = "50%";
    body.style.pointerEvents = "none";
}

// Function grabs the form and removes it from the page.
function closeForm(value) {

    // Grab the body and form elements.
    let body = document.getElementById("body");

    // Get the create group form.
    if (value === "C") {

        let form = document.getElementById("create-group-form");

        // Remove the form from the page.
        form.innerHTML = '';
    }

    // Get the view group form.
    if (value === "V") {

        let form = document.getElementById("view-group-form");

        // Remove the form from the page.
        form.innerHTML = '';
    }

    // Set the opacity of the main body back
    // to 100% and turn pointer events back on.
    body.style.opacity = "100%";
    body.style.pointerEvents = "all";
}


function manageGroupSubmit() {

    let groups = document.getElementsByClassName('user-group');
    for (let i = 0; i < groups.length; i++) {
        groups[i].addEventListener("click", () => groups[i].submit());
    }
}

manageGroupSubmit();


function viewGroup(g) {

    // Get the group info from the element.
    // 2 = ID, 3 = Status, 4 = Limit
    let gInfo = [g.children[0].innerText,
                 g.children[1].innerText,
                 g.children[2].value,
                 g.children[3].value,
                 g.children[4].value];

    if (gInfo[3] == 1) {
        gInfo[3] = "Public";
    }

    else {
        gInfo[3] = "Private";
    }

    let vgForm = document.getElementById("view-group-form");
    let body = document.getElementById("body");

    vgForm.innerHTML = `<form method="POST" action="php/form-handlers.php" class="view-group-form">

                            <div id="close-form" onclick="closeForm('V')">X</div>
                    
                            <div class="view-group-form-info">
                                <h3>${gInfo[0]}</h3>
                                <p> ${gInfo[1]}</p>
                            </div>
                    
                            <h4>Status</h4>
                            <p>${gInfo[3]}</p>
                    
                            <h4>Members</h4>
                            <p>${gInfo[4]}</p>
                            
                            <input type="hidden" id="gid" name="gid" value="${gInfo[2]}">
                            <input type="hidden" id="gName" name="Name" value="${gInfo[0]}">
                    
                            <input type="submit" id="join-group-submit" name="join-group-submit" value="Join Group">
                       </form>`

    // Set the opacity of the main body to 50%
    // and turn off pointer events.
    body.style.opacity = "50%";
    body.style.pointerEvents = "none";
}
