<?php
/** Template version: 3.1.0
 *
 * -= 3.1.0 =-
 * - Updated conversation-editor-replies-add-form template to allow image AJAX posting in replies
 *
 * -= 3.0.0 =-
 * - Improve UI for new master-skin
 *
 * -= 1.0.0 =-
 * - Initial version
 *
 */ ?>

<?php /** @var CUAR_Conversation $conversation */ ?>
<?php /** @var bool $enable_rich_editor */ ?>

<?php
$reply_content = isset($_POST['cuarme_reply_content']) ? $_POST['cuarme_reply_content'] : '';
$avatar_url = get_avatar_url(get_current_user_id(), array('size' => 64));
?>

<div class="cuar-reply-item cuar-js-add-reply-form">
    <div class="media">
        <div class="media-left">
            <img class="media-object" alt="64x64" src="<?php echo esc_attr($avatar_url); ?>">
        </div>
        <div class="media-body">
            <div class="cuar-js-manager-errors" style="display: none;">
                <div class="alert alert-danger alert-dismissable cuar-js-error-item">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <span class="cuar-js-error-content"></span>
                </div>
            </div>

            <div class="form-group">
                <div class="control-container cuar-js-reply-content">
                    <?php
                    $id = get_the_ID();
                    $post_type = get_post_type();

                    if ( ! $enable_rich_editor ) {
                        $field_code = printf( '<textarea rows="5" cols="40" name="cuarme_reply_content" id="cuarme_reply_content" class="form-control">%1$s</textarea>',
                            esc_attr( $reply_content ) );
                    } else {
                        $field_code = printf( '<input type="hidden" id="cuar_post_type" name="cuar_post_type" value="%1$s">'
                            . '<input type="hidden" id="cuar_post_id" name="cuar_post_id" value="%2$s">'
                            . '%3$s'
                            . '<textarea rows="5" cols="40" name="cuar_content" id="cuar_content" class="form-control cuar-js-richeditor">%4$s</textarea>',
                            $post_type,
                            $id,
                            wp_nonce_field( 'cuar_insert_image', 'cuar_insert_image_nonce' ),
                            esc_attr( $reply_content ));
                    }
                    ?>
                </div>
            </div>

            <div class="form-group">
                <div class="submit-container">
                    <input type="submit" name="cuarme_do_reply" disabled="disabled" value="<?php esc_attr_e('Reply',
                        'cuarme'); ?>" class="btn btn-default cuar-js-add-action"/>
                </div>
            </div>
        </div>
    </div>
</div>
