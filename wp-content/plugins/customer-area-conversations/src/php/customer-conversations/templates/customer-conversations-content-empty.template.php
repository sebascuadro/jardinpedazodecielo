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

<?php
$current_addon_slug = 'customer-conversations';
$current_addon_icon = apply_filters('cuar/private-content/view/icon?addon=' . $current_addon_slug, 'fa fa-comments');
$current_addon = cuar_addon($current_addon_slug);
$post_type = $current_addon->get_friendly_post_type();
?>

<div class="collection panel cuar-empty <?php echo $post_type; ?>">
    <div class="collection-content">
        <p class="mn"><?php _e( 'You currently have no conversations.', 'cuarme' ); ?></p>
    </div>
</div>