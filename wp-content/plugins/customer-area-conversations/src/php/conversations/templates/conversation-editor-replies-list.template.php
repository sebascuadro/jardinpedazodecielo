<?php
/** Template version: 3.0.0
 *
 * -= 3.0.0 =-
 * - Improve UI for new master-skin
 *
 * -= 1.0.0 =-
 * - Initial version
 *
 */ ?>

<?php /** @var string $item_template */ ?>
<?php /** @var string $form_template */ ?>
<?php /** @var CUAR_Conversation $conversation */ ?>

<?php
$is_conversation_author = ($conversation->post->post_author == get_current_user_id());
$replies = $conversation->get_replies();
$reply_count = count($replies);
?>

<div class="panel panel-default chat-widget cuar-js-conversation-manager" data-conversation-id="<?php echo esc_attr($conversation->ID); ?>">

    <?php wp_nonce_field('cuar-delete-reply-' . $conversation->ID, 'cuar_delete_reply_nonce'); ?>
    <?php wp_nonce_field('cuar-add-reply-' . $conversation->ID, 'cuar_add_reply_nonce'); ?>

    <div class="panel-heading">
        <span class="panel-icon"><i class="fa fa-comments"></i></span> <span class="panel-title"><?php _e('Replies', 'cuarme'); ?></span>
    </div>
    <div class="panel-body">
        <?php do_action('cuar/templates/single-post/conversation/before-replies'); ?>

        <div class="cuar-js-reply-list">
            <?php
            foreach ($replies as $reply)
            {
                include($item_template);
            }
            ?>
        </div>

        <div class="cuar-ajax-progress mt-md cuar-js-shown-when-loading" style="display:none;">
            <span class="cuar-loading-indicator"></span>
        </div>

        <p class="cuar-js-empty-message" <?php if ($reply_count > 0) echo 'style="display: none"'; ?>><?php _e('No replies yet', 'cuarme'); ?></p>

        <?php do_action('cuar/templates/single-post/conversation/after-replies'); ?>

        <?php
        $reply = null;
        include($item_template);
        ?>
    </div>
    <?php if ( !empty($form_template)) : ?>
        <div class="panel-footer">
            <?php include($form_template); ?>
        </div>
    <?php endif; ?>
</div>

<script type="text/javascript">
    <!--
    jQuery(document).ready(function ($) {
        $('.cuar-js-conversation-manager').conversationManager({
            userIsConversationAuthor: <?php echo ($is_conversation_author ? 'true' : 'false'); ?>
        });
    });
    //-->
</script>


