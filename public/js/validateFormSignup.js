$(document).ready(function () {
    /**
     * Validate the form
     */
    $('#formSignup').validate({
        rules: {
            name: 'required',
            email: {
                required: true,
                email: true,
                remote: '/account/validate-email'
            },
            password: {
                required: true,
                minlength: 6,
                validPassword: true
            }
        },
        messages: {
            name: "Podaj login",
            email: {
                required: "Podaj email",
                email: "Podany email jest niepoprawny",
                remote: "Podany email jest zajęty"
            },
            password: {
                required: "Podaj hasło",
                minlength: "Hasło musi zawierać co najmniej 6 znaków",
                validPassword: "Hasło musi zawierać przynajmniej jedną literę i jedną cyfrę"
            }
        },
        errorPlacement: function (error, element) {
            let name = $(element).attr("name");
            error.appendTo($("#" + name + "_validate"));
        }

    });

});