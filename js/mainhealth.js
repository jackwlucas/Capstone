'use strict';


// Function handles switching the ticket form.
function switchType(e) {

    // Grab the two ticket type buttons.
    let mType = document.getElementById("mType");
    let hType = document.getElementById("hType");

    // Grab the ticket form.
    let fForm = document.getElementById("ticket-form").children[0];

    // Grab two hidden values.
    let email = document.getElementById("EMAIL").innerText;
    let dorm  = document.getElementById("DORM").innerText;

    if (e.innerText === "Maintenance" && e.classList.contains("post-inactive")) {

        hType.classList.replace("post-active", "post-inactive");
        mType.classList.replace("post-inactive", "post-active");

        fForm.innerHTML = `<label for="maintenance"></label>
                           <input type="checkbox" id="maintenance" name="maintenance" checked hidden>
            
                           <label for="dorm">Dorm</label>
                           <input type="text" id="dorm" name="dorm" value="${dorm}" readonly>
            
                           <label for="floor">Floor Number</label>
                           <input type="number" id="floor" name="floor" required>
            
                           <label for="room">Room Number</label>
                           <input type="number" id="room" name="room" required>
            
                           <label for="issue">Issue</label>
                           <input type="text" id="issue" name="issue" required>
            
                           <label for="description">Description</label>
                           <input type="text" id="description" name="description" required>`;
    }

    if (e.innerText === "Health" && e.classList.contains("post-inactive")) {

        hType.classList.replace("post-inactive", "post-active");
        mType.classList.replace("post-active", "post-inactive");

        fForm.innerHTML = `<label for="health"></label>
                           <input type="checkbox" id="health" name="health" checked hidden>
            
                           <label for="email">Email</label>
                           <input type="text" id="email" name="email" value="${email}" readonly>
            
                           <label for="issue">Issue</label>
                           <input type="text" id="issue" name="issue" required>
            
                           <label for="description">Description</label>
                           <input type="text" id="description" name="description" required>`
    }
}


// Opens the ticket forms.
function openForm() {

    // Grab the body and form elements.
    let body = document.getElementById("body");
    let form = document.getElementById("ticket-window");

    // If the form is hidden, lower the opacity of the
    // background and reveal the form.
    body.style.pointerEvents = "none";
    body.style.opacity = "20%";
    form.style.display = "block";
}


// Closes the ticket forms.
function closeForm() {

    // Grab the body and form elements.
    let body = document.getElementById("body");
    let form = document.getElementById("ticket-window");

    // If the form is hidden, lower the opacity of the
    // background and reveal the form.
    body.style.pointerEvents = "all";
    body.style.opacity = "100%";
    form.style.display = "none";
}


//
function resolveTicket(ticket) {

    // Grab the ticket type and ID.
    let type = ticket.classList[0];
    let id = ticket.children[0].value;

    // Grab the body and form elements.
    let body = document.getElementById("body");
    let form = document.getElementById('resolve-ticket-form');

    // If the form is hidden, lower the opacity of the
    // background and reveal the form.
    body.style.pointerEvents = "none";
    body.style.opacity = "20%";
    form.style.display = "block";

    form.innerHTML = `<div class="resolve-ticket-form">
                          <h2>Resolve Ticket</h2>
                            
                          <h4>Are you sure you want to resolve this ticket?<br>
                              <span>This cannot be undone.</span></h4>
                        
                          <div id="resolve-ticket-buttons">
                              <input type="button" id="cancel-button" value="Cancel">
                              <input type="button" id="delete-button" value="Resolve">
                          </div>
                      </div>`;

    let buttons = document.querySelector('#resolve-ticket-buttons');
    buttons = [buttons.children[0], buttons.children[1]];

    buttons[0].addEventListener('click', () => {

        // If cancel is clicked, return the body
        // to normal and clear the form.
        body.style.pointerEvents = "all";
        body.style.opacity = "100%";
        form.innerHTML = '';
    })

    buttons[1].addEventListener('click', () => {

        body.innerHTML += `<form method="POST" action="php/form-handlers.php" id="resolve" style="display: none">
                                
                                <input type="hidden" value="${type}" name="ticket-Type">
                                <input type="hidden" value="${id}"   name="ticket-ID">
                                <input type="submit" value="" name="resolve-ticket-submit">
                                
                           </form>`

        document.querySelector('#resolve').children[2].click();
    })
}