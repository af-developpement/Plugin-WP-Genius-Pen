<?php
$nonce = wp_create_nonce( 'generate_article_nonce' );
echo '
    </form>
    <form action="'. esc_url( admin_url( 'admin-post.php' ) ) .'" method="post" id="form-content-generator">
        <div class="input-container" style="margin-bottom: 2rem;">
            <input type="range" id="nbrTokens" name="nbrTokens" min="50" max="4000" value="18" oninput="this.nextElementSibling.value = this.value">
            &nbsp;&nbsp;&nbsp;<output>50</output><span> Tokens</span>
        </div>
        <div class="input-container" style="margin-bottom: 2rem;">
        	<label for="key-words" style="display: block; margin-bottom: .5rem;">Vos mots clés</label>
        	<small>Au maximum 4, séparé par des virgules ','</small>
            <input type="text" id="key-words" name="key-words" placeholder="mot-clé 1, mot-clé 2" style="width: 100%;">
        </div>
        <a href="https://help.openai.com/en/articles/4936856-what-are-tokens-and-how-to-count-them" target="_blank" style="display: flex; justify-content: center; align-items: center; grid-gap: 5px;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M12 3.2c-4.8 0-8.8 3.9-8.8 8.8 0 4.8 3.9 8.8 8.8 8.8 4.8 0 8.8-3.9 8.8-8.8 0-4.8-4-8.8-8.8-8.8zm0 16c-4 0-7.2-3.3-7.2-7.2C4.8 8 8 4.8 12 4.8s7.2 3.3 7.2 7.2c0 4-3.2 7.2-7.2 7.2zM11 17h2v-6h-2v6zm0-8h2V7h-2v2z"></path></svg>en savoirs plus
        </a>
        
        ';

        if(is_plugin_active('classic-editor/classic-editor.php')) {
                echo '<small style="display: block; margin-top: 2rem;">Vous devez impérativement avoir le focus sur l\'éditeur pour que le texte s\'affiche</small>';
        }

echo '
        <input type="hidden" name="generate_article_nonce" value="'.$nonce.'" />            
        <input type="submit" value="Générer" class="button button-primary mt-5" style="margin-top: 1rem;" />
    </form>
    
    <div id="content-generator-response"></div>
';