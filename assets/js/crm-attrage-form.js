jQuery(document).ready(function($) {
    $("#crm-attrage-lead-form").on("submit", function(e) {
        e.preventDefault();

        var $form = $(this);
        var $messageDiv = $("#crm-attrage-form-message");
        var formData = $form.serialize();

        $messageDiv.removeClass("success error").html("Enviando...");
        $form.find("input[type=submit]").prop("disabled", true);

        $.ajax({
            url: crmAttrage.ajax_url,
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $messageDiv.addClass("success").html(response.data.message);
                    $form[0].reset(); // Limpa o formulário após o sucesso
                } else {
                    $messageDiv.addClass("error").html(response.data.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $messageDiv.addClass("error").html("Ocorreu um erro ao enviar o formulário. Tente novamente.");
                console.error("AJAX Error: ", textStatus, errorThrown, jqXHR);
            },
            complete: function() {
                $form.find("input[type=submit]").prop("disabled", false);
            }
        });
    });
});
