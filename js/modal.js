// Get the modal
var modal = document.getElementById("info-modal");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("modal-close")[0];

// When the user clicks on <span> (x), close the modal
span.onclick = function () {
    document.getElementById('modal-content-div').innerHTML = "";
    document.getElementById('modal-content-comment').innerHTML = "";
    document.getElementById("info-modal").style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function (event) {
    if (event.target.id == "info-modal") {
        document.getElementById('modal-content-div').innerHTML = "";
        document.getElementById('modal-content-comment').innerHTML = "";
        document.getElementById("info-modal").style.display = "none";
    }
}