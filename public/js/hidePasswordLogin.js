function password_show_hide_login() {
    let x = document.getElementById("inputPassword");
    let show_eye_login = document.getElementById("show_eye_login");
    let hide_eye_login = document.getElementById("hide_eye_login");
    hide_eye_login.classList.remove("d-none");
    if (x.type === "password") {
        x.type = "text";
        show_eye_login.style.display = "none";
        hide_eye_login.style.display = "block";
    } else {
        x.type = "password";
        show_eye_login.style.display = "block";
        hide_eye_login.style.display = "none";
    }
}