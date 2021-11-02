var recaptcha_response = '';

function submitUserForm() {
    if (recaptcha_response.length == 0) {
        document.getElementById('g-recaptcha-error').innerHTML =
            '<span style="color:black;"> Udowodnij, że nie jesteś robotem </span>';
        return false;
    }
    return true;
}

function verifyCaptcha(token) {
    recaptcha_response = token;
    document.getElementById('g-recaptcha-error').innerHTML = '';
}