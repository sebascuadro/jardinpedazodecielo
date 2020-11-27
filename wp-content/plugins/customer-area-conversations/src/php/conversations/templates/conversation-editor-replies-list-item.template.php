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

<?php /** @var CUAR_Conversation $conversation */ ?>
<?php /** @var CUAR_ConversationReply $reply */ ?>

<?php
$item_class = "cuar-reply-item cuar-js-reply ";
if ($reply != null)
{
    $reply_author = get_userdata($reply->post->post_author);
    $is_author_of_conversation = $conversation->post->post_author == $reply_author->ID;

    $content = $reply->post->post_content;
    $date = get_the_date('', $reply->ID);
    $time = get_the_time('', $reply->ID);
    $author = $reply_author->display_name;
    $avatar_url = get_avatar_url($reply->post->post_author, array('size' => 64));

    $item_class .= $is_conversation_author ? 'cuar-author-reply' : 'cuar-participant-reply';
}
else
{
    $reply_author = '';
    $is_author_of_conversation = false;
    $content = '';
    $date = '';
    $time = '';
    $author = '';
    $avatar_url = '';
    $item_class .= 'cuar-js-reply-template';
}
?>
<div class="media <?php echo $item_class; ?>" id="cuar_reply_<?php echo $reply==null ? 0 : $reply->ID; ?>" data-reply-id="<?php echo esc_attr($reply == null
    ? 0 : $reply->ID); ?>" data-conversation-id="<?php echo esc_attr($conversation->ID); ?>"  <?php if ($reply == null) echo 'style="display: none;"'; ?>>
    <?php if ($reply == null || !$is_author_of_conversation) : ?>
        <div class="media-left cuar-js-hide-when-author">
            <img class="media-object cuar-js-avatar" alt="64x64" src="<?php echo esc_attr($avatar_url); ?>">
        </div>
    <?php endif; ?>
    <div class="media-body">
        <div class="cuar-title media-heading">
            <span class="cuar-js-author"><?php echo $author; ?></span>
            <small class="cuar-js-timestamp"><?php echo $date; ?> - <?php echo $time; ?></small>
        </div>
        <div class="cuar-js-content mt-sm">
            <?php echo $content; ?>
        </div>
        <div class="cuar-js-actions mt-sm text-right">
            <?php if ($reply == null || cuar_user_can_delete_conversation_reply($conversation->ID, $reply->ID)) : ?>
                <a href="#" class="btn btn-xs btn-default cuar-js-delete-action"><?php _e('Delete reply', 'cuarme'); ?></a>
            <?php endif; ?>
        </div>
    </div>
    <?php if ($reply == null || $is_author_of_conversation) : ?>
        <div class="media-right cuar-js-show-when-author">
            <img class="media-object cuar-js-avatar" alt="64x64" src="<?php echo esc_attr($avatar_url); ?>">
        </div>
    <?php endif; ?>
</div>
