$(document).ready(function () {
    /**
     * Validate the form
     */
    $('#formLogin').validate({
        rules: {
            email: {
                required: true,
                email: true,
            },
            password: {
                required: true,
                validPassword: true
            }
        },
        messages: {
            email: {
                required: "Podaj email",
                email: "Podany email jest niepoprawny",
            },
            password: {
                required: "Podaj hasło",
            }
        },
        errorPlacement: function (error, element) {
            let name = $(element).attr("name");
            error.appendTo($("#" + name + "_validate"));
        }
    });
});