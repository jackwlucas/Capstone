'use strict';
console.log("JavaScript working.");


// Function handles switching the posting form.
function switchType(e) {

    // Grab the two post type buttons.
    let aType = document.getElementById("aType");
    let eType = document.getElementById("eType");

    // Grab the post form.
    let pForm = document.getElementById("post-form").children[0];

    // This switches the styling if the user selects announcements, and they
    // are currently inactive.
    if (e.innerText === "Announcement" && e.classList.contains("post-inactive")) {

        eType.classList.replace("post-active", "post-inactive");
        aType.classList.replace("post-inactive", "post-active");

        pForm.innerHTML = `<input type="checkbox" id="announcement" name="announcement" checked hidden>
                           
                           <label for="aTitle">Title</label>
                           <input type="text" placeholder="Enter Title" id="aTitle" name="aTitle" required>
                            
                           <label for="aDesc">Description</label>
                           <input type="text" placeholder="Enter Description" id="aDesc" name="aDesc" required>`;
    }

    // This switches the styling if the user selects events, and they are
    // currently inactive.
    if (e.innerText === "Event" && e.classList.contains("post-inactive")) {

        eType.classList.replace("post-inactive", "post-active");
        aType.classList.replace("post-active", "post-inactive");

        pForm.innerHTML = `<input type="checkbox" id="event" name="event" checked hidden>
    
                           <label for="eTitle">Title</label>
                           <input type="text" placeholder="Enter Title" id="eTitle" name="eTitle" required>
          
                           <label for="eDesc">Description</label>
                           <input type="text" placeholder="Enter Description" id="eDesc" name="eDesc" required>
      
                           <label for="eLocation">Location</label>
                           <input type="text" placeholder="Enter Location" id="eLocation" name="eLocation" required>
            
                           <label for="eDate">Date</label>
                           <input type="date" id="eDate" name="eDate" required>
                           
                           <label for="eTime">Time</label>
                           <input type="time" id="eTime" name="eTime" required>`;
    }
}


// Function takes a form and adds it to the page.
function openForm(value) {

    if (value === "P") {

        // Check to see if the user is in any groups.
        $(document).ready(() => {

            $.ajax({

                url: "php/ajax-handlers.php",
                method: "POST",
                data: {createPostButton:1},
                success: (data) => {

                    // If they are in a group, display the form.
                    if (data > 0) {
                        // Grab the body and form elements.
                        let body = document.getElementById("body");
                        let form = document.getElementById("post-window");

                        // If the form is hidden, lower the opacity of the
                        // background and reveal the form.
                        body.style.pointerEvents = "none";
                        body.style.opacity = "20%";
                        form.style.display = "block";
                    }

                    else {
                        $('.container').append('<p class="alert" id="alert">You need to join a group to make a post!</p>');
                        setTimeout(() => {
                            $('#alert').fadeOut(500, () => { $('#alert').remove(); });
                        }, 2000);
                    }
                }
            })
        })
    }

    else {

        // Grab the necessary elements.
        let body = document.getElementById("body");
        let form = document.getElementById("delete-form");
        let delB = document.getElementById("post-delete");

        // Set the data from the delete button.
        let p = delB.value;



        // Grab the form on the page, and remove it.
        body.style.opacity = "50%";
        body.style.pointerEvents = "none";
    }


}

// Function grabs a form and removes it from the page.
function closeForm(value) {

    if (value === "P") {

        let body = document.getElementById("body");
        let form = document.getElementById("post-window");

        // Grab the form on the page, and remove it.
        body.style.opacity = "100%";
        body.style.pointerEvents = "all";
        form.style.display = "none";
    }

    if (value === "D") {

        let body = document.getElementById("body");
        let form = document.getElementById("delete-form");

        form.innerHTML = '';

        // Grab the form on the page, and remove it.
        body.style.opacity = "100%";
        body.style.pointerEvents = "all";
    }

    if (value === "R") {

        let form = document.getElementById("reply-form");

        form.classList.remove('reply-form');
        form.remove();
    }
}


function likePost(post, type) {

    let svg = post;
    let cnt = post.children[1];

    if (svg.classList.contains("post-like-active")) {
        svg.classList.remove("post-like-active");
        svg.classList.add("post-like-inactive");
        cnt.innerText = parseInt(cnt.innerText) - 1;
    }

    else {
        svg.classList.remove("post-like-inactive");
        svg.classList.add("post-like-active");
        cnt.innerText = parseInt(cnt.innerText) + 1;
    }

    // Initialize an empty variable to store the like buttons.
    let hearts = [];
    let pos = 0;

    // Grab the relevant like buttons based upon the type passed to the function.
    switch(type) {
        case "Post":
            hearts = document.getElementsByClassName("post-hearts");
            pos = parseInt(post.closest(".timeline-post").offsetTop);
            break;
        case "Comment":
            hearts = document.getElementsByClassName("comment-hearts");
            pos = parseInt(post.closest(".post-comment").offsetTop);
            break;
    }

    // Set a cookie to store the position of the button clicked.
    setCookie('pos', pos, 1);

    // Initialize an index variable equal to -1.
    // Grab the index position of the like button clicked.
    let indx = -1;
    for (let i = 0; i < hearts.length; i++) {

        // Set the index if we find the button we clicked.
        if (hearts[i] === post) {

            indx = i;
            break;
        }
    }

    // Get the liking form.
    let form = post.parentElement;

    // Create a new input element containing the index position.
    let input = document.createElement("input");
    input.type  = "hidden";
    input.name  = "index";
    input.value = indx.toString();

    // Append the index input to the form.
    form.appendChild(input);

    // Submit the form.
    form.children[2].click();

}


function replyPost(post) {

    if (document.getElementById('reply-form')) {

        closeForm("R");
    }

    let element = (post.parentElement).parentElement;

    // Create the input elements for the user.
    let HTML = document.createElement(`div`);
    HTML.classList.add('reply-form');
    HTML.id = 'reply-form';
    HTML.innerHTML = `<label for="comment">Comment</label>
                      <textarea id="comment" maxlength="256" rows="4" cols="30"></textarea>
                        
                      <div>
                          <input type="button" value="Cancel" onclick="closeForm('R')">
                          <input type="button" id="reply-form-button" value="Reply">
                      </div>`;

    // Insert the form after the corresponding post.
    element.after(HTML);

    // Add onclick to reply button.
    let btn = document.getElementById('reply-form-button');

    btn.addEventListener("click", () => {

        // Get the posts.
        let posts = document.getElementsByClassName('timeline-post');

        // Set the index.
        let indx = -1;

        // Grab the index position of the comment button clicked.
        for (let i = 0; i < posts.length; i++) {

            // If the element clicked matches the element
            // on the page, return the index.
            if (posts[i] === element) {

                indx = i;
                break;
            }
        }

        // Grab the comment from the user's input and the page body.
        let body = document.getElementById('body');
        let comment = document.getElementById('comment').value;

        if (comment.length !== '') {
            // Build a hidden form that will be submitted.
            body.innerHTML += `<form method="POST" action="php/form-handlers.php" name="post-reply-form" id="post-reply-form" style="display: none">
                                    <input type="hidden" name="index"   value="${indx}">
                                    <input type="hidden" name="comment" value="${comment}">
                                    <input type="submit" name="post-reply-button" id="post-reply-button">
                               </form>`;

            // Finally, get the hidden form and submit it.
            document.getElementById('post-reply-button').click();
        }
    });
}


function deletePost(post, type) {

    // Grab the necessary elements.
    let body = document.getElementById("body");
    let form = document.getElementById("delete-form");

    let dels = [];

    // Grab the relevant like buttons based upon the type passed to the function.
    switch(type) {
        case "Post":
            dels = document.getElementsByClassName("post-delete");
            break;
        case "Comment":
            dels = document.getElementsByClassName("comment-delete");
            break;
    }

    // Initialize an index variable equal to -1.
    // Grab the index position of the like button clicked.
    let indx = -1;
    for (let i = 0; i < dels.length; i++) {

        // Set the index if we find the button we clicked.
        if (dels[i] === post) {

            indx = i;
            break;
        }
    }

    // Darken the background and remove pointer events.
    body.style.opacity = "50%";
    body.style.pointerEvents = "none";

    // Insert the form into the page.
    form.innerHTML = `<form class="delete-form" method="POST">

                        <h2>Delete Post</h2>

                        <h4>Are you sure you want to delete this post?<br>
                        <span>This cannot be undone.</span></h4>
                            
                        <div id="delete-form-buttons">
                            <input type="button" id="cancel-button" value="Cancel">
                            <input type="button" id="delete-button" value="Delete">
                        </div>
                       
                      </form>`;


    let buttons = document.getElementById("delete-form-buttons");
    buttons = [buttons.children[0], buttons.children[1]];

    buttons[0].onclick = () => {

        form.innerHTML = '';
        body.style.opacity = "100%";
        body.style.pointerEvents = "all";
    }

    buttons[1].onclick = () => {

        // If a post delete button was clicked, create the form
        // with the appropriate submit button.
        if (type === "Post") {

            body.innerHTML += `<form method="POST" action="php/form-handlers.php" id="post-delete-action" style="display: none;">
                                    <input type="hidden" id="index" name="index" value="${indx}">
                                    <input type="submit" name="post-delete-button">
                               </form>`;
        }

        // If a comment delete button was clicked, create the form
        // with the appropriate submit button.
        if (type === "Comment") {

            body.innerHTML += `<form method="POST" action="php/form-handlers.php" id="post-delete-action" style="display: none;">
                                    <input type="hidden" id="index" name="index" value="${indx}">
                                    <input type="submit" name="comment-delete-button">
                               </form>`;
        }


        // Submit the form.
        document.getElementById("post-delete-action").children[1].click();
    }
}


// Function creates a cookie that is stored in the browser.
function setCookie(name,value,days) {

    let expires = "";

    if (days) {

        let date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));

        expires = "; expires=" + date.toUTCString();
    }

    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}


// Function retrieves the value of a cookie.
function getCookie(name) {

    // Set up the name.
    let cName = name + "=";
    let ca = document.cookie.split(';');
    for(let i = 0; i < ca.length; i++) {

        // Grab the current character.
        let c = ca[i];

        while (c.charAt(0) === ' ') c = c.substring(1, c.length);

        if (c.indexOf(cName) === 0) {

            return c.substring(cName.length, c.length);

        }
    }

    return null;
}


// Function deletes a cookie from the browser.
function deleteCookie(name) {
    document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 2000 00:00:01 EST;';
}


// Code block handles scrolling to page location of post
// after user likes a post.
if (getCookie('pos')) {

    // Get the value from the cookie, scroll to the
    // location, and delete the cookie.
    let scroll = parseInt(getCookie('pos'));
    window.scrollTo(0, scroll - 300);
    deleteCookie('pos');
}
