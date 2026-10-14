console.log("business card ready");
const banner = document.querySelector("#proto-banner");
const bannerClose = document.querySelector("#proto-close");
if (banner && bannerClose) {
    let seen = false;
    try { seen = localStorage.getItem("protoBannerSeen") === "1"; } catch (e) {}
    if (seen) banner.hidden = true;
    bannerClose.addEventListener("click", function () {
        banner.hidden = true;
        try { localStorage.setItem("protoBannerSeen", "1"); } catch (e) {}
    });
}

const form = document.querySelector("#contact-form") || document.querySelector("form");
const csrfField = form.querySelector("[name=csrf]");
if (csrfField) {
    fetch("php/csrf.php", { credentials: "same-origin" })
        .then(function (response) { return response.json(); })
        .then(function (data) { csrfField.value = data.csrf; })
        .catch(function () { console.log("could not load CSRF token"); });
}
form.addEventListener("submit", function (event) {
    const name = form.querySelector("[name=name]").value.trim();
    const email = form.querySelector("[name=email]").value.trim();
    const message = form.querySelector("[name=message]").value.trim();

    if (!name || !email || !message || email.indexOf("@") === -1) {
        event.preventDefault();
        console.log("blocked empty contact form");
        alert("Please fill in name, email, and message.");
    }
});

const filterButtons = document.querySelectorAll(".filter");
const writeupList = document.querySelectorAll("#writeup-list article");

function applyFilter(tag) {
    filterButtons.forEach(function (btn) {
        btn.classList.toggle("is-active", btn.dataset.filter === tag);
    });

    writeupList.forEach(function (writeup) {
        writeup.classList.toggle("is-hidden", tag !== "all" && (!writeup.dataset.tag || writeup.dataset.tag !== tag));
    });
}

filterButtons.forEach(function (btn) {
    btn.addEventListener("click", function () {
        applyFilter(btn.dataset.filter);
    });
});

const themeToggle = document.querySelector("#theme-toggle");

themeToggle.addEventListener("click", function () {
    const isLight = document.body.classList.toggle("light-theme");
    console.log("theme:", isLight ? "light" : "dark");
});

const sections = document.querySelectorAll("section[id]");
const navLinks = document.querySelectorAll("nav a");

const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            const id = entry.target.getAttribute("id");
            navLinks.forEach((link) => {
                link.classList.toggle("is-current", link.getAttribute("href") === `#${id}`);
            });
            console.log("nav highlight:", id);
        }
    });
}, {
    rootMargin: "-20% 0px -70% 0px" // focuses detection in the active reading area
});

sections.forEach((section) => {
    observer.observe(section);
});
// Bug I hit: toggled "dark-mode" in JS but CSS was watching for "light-theme" — nothing visibly changed until I matched the class names.