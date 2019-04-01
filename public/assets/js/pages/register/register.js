$(document).ready(function(){
    $("#createButton").click(function(e){
        $("#strength").html('');

        e.preventDefault();
        if ($("#password").val() == '')
        {
            $("#createForm").submit();
            return true;
        }

        var result = zxcvbn($("#password").val());

        if (result.score < passwordStrength)
        {
            var passwordStrengthWarning = 'This password cannot be used, as it is too weak. ';
            if (result.feedback.warning != '') {
                passwordStrengthWarning = passwordStrengthWarning + " " + result.feedback.warning;
            }
            if (result.feedback.suggestions.length != 0) {
                passwordStrengthWarning = passwordStrengthWarning + ". " + result.feedback.suggestions[0];
            }
            $("#strength").html(passwordStrengthWarning);
            return false;
        }
        else
        {
            $("#createForm").submit();
            return true;
        }
    });
});

