<?php
/** Template version: 3.1.0
 *
 * -= 3.1.0 =-
 * - Color reply count when unread replies + do not show when no replies at all
 * - Add row hooks
 *
 * -= 3.0.0 =-
 * - Improve UI for new master-skin
 *
 * -= 1.1.0 =-
 * - Updated markup
 * - Normalized the extra class filter name
 *
 * -= 1.0.0 =-
 * - Initial version

 */ ?>

<?php
global $post;

$current_user_id = get_current_user_id();
$conversation = new CUAR_Conversation($post);

$has_new_replies = $conversation->has_new_replies($current_user_id);
$reply_count = $conversation->get_reply_count();

if ($has_new_replies) {
    $title_popup = sprintf(__('Latest reply on %s', 'cuarme'), get_the_modified_time(get_option('date_format')));
} else {
    $title_popup = sprintf(__('Started on %s', 'cuarme'), get_the_date());
}

$extra_class = $has_new_replies ? ' cuar-updated' : '';
?>

<tr class="<?php echo $extra_class; ?>">
    <td class="cuar-title">
        <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr($title_popup); ?>">
            <?php the_title(); ?>
        </a>
    </td>
    <td class="text-right cuar-extra-info">
        <?php do_action('cuar/templates/block/item/extra-info'); ?>
        <?php if ($reply_count>0) : ?>
            <span class="label label-<?php echo $has_new_replies ? 'primary' : 'default'; ?> cuar-replies" title="<?php echo esc_attr(sprintf(_n('%d reply', '%d replies', $reply_count, 'cuarme'), $reply_count)); ?>">
                <i class="fa fa-comments-o"></i>&nbsp;<?php echo $reply_count; ?>
            </span>
        <?php endif; ?>
    </td>
</tr>