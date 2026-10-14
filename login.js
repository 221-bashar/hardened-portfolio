// Fetch this visitor's CSRF token and put it in the login form.
const csrfField = document.querySelector("#csrf");
if (csrfField) {
    fetch("/php/csrf.php", { credentials: "same-origin" })
        .then(function (response) { return response.json(); })
        .then(function (data) { csrfField.value = data.csrf; })
        .catch(function () { console.log("could not load CSRF token"); });
}