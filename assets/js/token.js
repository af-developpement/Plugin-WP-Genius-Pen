jQuery(document).ready(function ($) {
    $('#form-token-registration #af-verification-token').click(function (event) {
        event.preventDefault();

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'token_verification',
                nonce: $('input[name=generate_article_nonce]').val(),
                token: $('#af-article-token').val(),
            },
        })
        .done(function (response) {
            if (response.success) {
                $("#content-generator-response").html( "<h3 style='color: green;'>Le token est validé</h3><br>" );
            }
        })
        .fail(function (jqXHR) {
            if (jqXHR.status === 404) {
                $("#content-generator-response").html( "<h3 style='color: red;'>Le token est invalide</p><br>" );
            }
        });
    });
});

