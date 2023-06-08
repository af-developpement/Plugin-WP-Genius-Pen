<div class="wrap">
	<h2>Ajouter votre token</h2>
	<p>Si vous n'en avez pas, créer en un <a href="https://wp-ai-writter.com/domaine" target="_blank" title="redirection vers le site d'achat du token">ici.</a></p>
	<form method="post" action="options.php" id="form-token-registration">
		<?php settings_fields('af_generate_article_options_group'); $nonce = wp_create_nonce( 'token_verification_nonce' ); ?>

		<table class="form-table">
			<tr>
				<th><label for="af-generate-article-token">Token :</label></th>

				<td>
					<input type="text" class="regular-text" id="af-article-token" name="af_generate_article_token" value="<?= get_option('af_generate_article_token') ?>">
				</td>

                <td>
                    <input type="hidden" name="generate_article_nonce" value="<?= $nonce ?>" />
                    <button type="button" class="regular-text" id="af-verification-token" name="af-verification-token">Vérifier</button>
                    <span class="code">
                        <code>Vous aller lier votre token à ce site.</code>
                    </span>
				</td>
			</tr>
		</table>

        <div id="content-generator-response"></div>

		<?php submit_button(); ?>
    </form>
</div>