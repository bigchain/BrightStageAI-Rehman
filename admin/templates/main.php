<?php
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'Unauthorized' );
}
?>
<div class="wrap">
    <h1>BrightsideAI - OpenAI Settings</h1>
    <?php if ( isset( $_GET['updated'] ) ) : ?>
        <div class="notice notice-success"><p>Settings saved.</p></div>
    <?php endif; ?>
    <h2>OpenAI API Key</h2>
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <?php wp_nonce_field( 'brightsideai_save_api_key', 'brightsideai_nonce' ); ?>
        <input type="hidden" name="action" value="brightsideai_save_api_key">
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><label for="brightsideai_api_key">API Key</label></th>
                <td><input type="text" id="brightsideai_api_key" name="brightsideai_api_key" value="<?php echo esc_attr( get_option('brightsideai_openai_key') ); ?>" class="regular-text" /></td>
            </tr>
        </table>
        <?php submit_button( 'Save API Key' ); ?>
    </form>

    <h2>Generate Text</h2>
    <form id="brightsideai-generate-text-form">
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><label for="brightsideai_prompt">Prompt</label></th>
                <td>
                    <textarea id="brightsideai_prompt" name="prompt" rows="5" style="width:100%;" class="regular-text"></textarea>
                </td>
            </tr>
        </table>
        <!-- Add nonce field for AJAX -->
        <input type="hidden" id="brightsideai_nonce" name="nonce" value="<?php echo wp_create_nonce('brightsideai_nonce'); ?>" />
        <p class="submit">
            <button type="submit" class="button button-primary">Generate Text</button>
        </p>
    </form>
    <h3>Generated Text:</h3>
    <textarea id="brightsideai-generated-text" rows="5" style="width:100%;background:#f7f7f7;padding:10px;"></textarea>
</div>
