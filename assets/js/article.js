jQuery( document ).ready( function( $ ) {
    $( '#form-content-generator' ).submit(function( event ) {
        let confirmed = window.confirm("Êtes-vous sûr de vouloir supprimer l'élément ?");
        
        event.preventDefault();
        if (!confirmed) {
            return;
        }


        let txt = $('#wpbody #editor h1').text();

        if($('#wpbody #title').length === 1){
            txt = $('#wpbody #title').val();
        }

        const submitBtn = $(this).find('input[type="submit"]');
        submitBtn.prop('disabled', true);
        const loader = '<span class="spinner is-active" style="float: none; display: inline-block; margin-left: 10px;"></span>';
        submitBtn.after(loader);

        $.ajax({
            type:   'POST',
            url:    ajaxurl,
            data: {
                nonce: $("input[name=generate_article_nonce]").val(),
                title: txt,
                nbrTokens: $('#nbrTokens').val(),
                keyWords: $('#key-words').val(),
                action: 'form_content_generator',
            }
        })
        .success( function( response ) {
            const content = JSON.parse(response.data.content);
            console.log(content.status)

            if(content.status === 400) {
                $("#content-generator-response").html(`
                    <h3 style="color: red;">${content.message}</h3>
                    <p style="color: red;">Aller sur <a style='display: contents;' href='https://wp-genius-pen.com/'>wp-genius-pen.com</a></p>
                    <br>
                `);
            } else if(response.data.success){
                if(response.data.isClassicEditor) {
                    tinymce.activeEditor.setContent(JSON.parse(response.data.content).post);
                } else {
                    const newBlock = wp.blocks.createBlock( "core/paragraph", {
                        content: JSON.parse(response.data.content).post,
                    });
                    wp.data.dispatch( "core/block-editor" ).insertBlocks( newBlock );
                }
            }
        })
        .error(function (jqXHR) {
            if (jqXHR.status === 402) {
                $("#content-generator-response").html(`
                    <h3 style="color: red;">Erreur: nombre de jetons ou abonnement invalide.</h3>
                    <p style="color: red;">Aller sur <a style='display: contents;' href='https://wp-genius-pen.com/'>wp-genius-pen.com</a></p>
                    <br>
                `);
            }

            if (jqXHR.status === 403) {
                $("#content-generator-response").html(`
                    <h3 style="color: red;">Le token est invalide ou l'url n'est pas lié à votre compte</h3>
                    <p style="color: red;">Aller dans les réglages du plug-in pour y associé le bon token provenant de <a style='display: contents;' href='https://wp-genius-pen.com/domaine'>wp-genius-pen.com/domaine</a></p>
                    <br>
                `);
            }

            if (jqXHR.status === 404) {
                $("#content-generator-response").html(`
                    <h3 style="color: red;">Le token est invalide</h3>
                    <p style="color: red;">Aller dans les réglages du plug-in pour y associé le bon token provenant de <a style='display: contents;' href='https://wp-genius-pen.com/domaine'>wp-genius-pen.com/domaine</a></p>
                    <br>
                `);
            }
        })
        .always(function () {
            submitBtn.prop('disabled', false);
            $('.spinner').remove();
        });
    });
});
