<?php
/** Template version: 3.1.0
 *
 * -= 3.1.0 =-
 * - Replace clearfix CSS classes with cuar-clearfix
 *
 * -= 3.0.0 =-
 * - Initial version
 *
 */
?>

<?php
global $post;

$is_author = get_the_author_meta('ID') == get_current_user_id();

if ($is_author) {
    $published = sprintf(__('Published on %s, by yourself, for %s', 'cuarse'), get_the_date(), cuar_get_the_owner());
} else {
    $published = sprintf(__('Published on %s, by %s, for %s', 'cuarse'), get_the_date(), get_the_author_meta('display_name'), cuar_get_the_owner());
}

$extra_class = ' ' . get_post_type();
$extra_class = apply_filters('cuar/templates/list-item/extra-class?post-type=' . get_post_type(), $extra_class, $post);
?>

<div class="search-result cuar-clearfix<?php echo $extra_class; ?>">
    <div class="cuar-title result-title">
        <a href="<?php the_permalink(); ?>">
            <?php the_title(); ?>
        </a>
    </div>
    <ul class="result-meta">
        <li><?php echo $published; ?></li>
    </ul>
    <p class="result-excerpt"><?php echo get_the_excerpt(); ?></p>
</div>