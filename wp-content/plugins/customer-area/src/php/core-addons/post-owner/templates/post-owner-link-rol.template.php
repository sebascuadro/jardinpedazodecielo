<?php /**
 * Template version: 1.0.0
 *
 * -= 1.0.0 =-
 * - Initial version
 *
 */ ?>

<?php
/** @var $owner_id integer The owner ID */
/** @var $owner_type string The owner type */

$po_addon = cuar_addon('post-owner');
$owner_links = $po_addon->get_owner_submenu_links($owner_id, $owner_type);
$owner_data_name = $po_addon->get_owner_display_name($owner_type, $owner_id);
$owner_data_title = sprintf(esc_attr__('This content is assigned to the role: %1s', 'cuar'), $owner_data_name);
?>

<div class="cuar-meta-link cuar-meta-link-<?php echo $owner_type; ?> cuar-js-dropdown-in-overflow pull-left">
    <a href="#"
       class="btn btn-xs btn-default dropdown-toggle mr5"
       data-toggle="dropdown" aria-haspopup="true"
       aria-expanded="false"
       title="<?php echo $owner_data_title; ?>">
        <i class="fa fa-user pn"></i>&nbsp;<?php echo $owner_data_name; ?>
    </a>
    <?php if ($owner_links) { ?>
        <ul class="cuar-js-dropdown-in-overflow-menu dropdown-menu">
            <?php foreach ($owner_links as $owner_link) {
                if (isset($owner_link['type']) && $owner_link['type'] === 'divider') { ?>
                    <li class="divider"></li>
                    <?php
                } else {
                    ?>
                    <li>
                        <a href="<?php echo esc_url($owner_link['url']); ?>"
                           title="<?php echo esc_attr($owner_link['tooltip']); ?>"
                           class="<?php echo esc_attr($owner_link['extra_class']); ?>">
                            <?php echo $owner_link['title']; ?>
                        </a>
                    </li>
                <?php } ?>
            <?php } ?>
        </ul>
    <?php } ?>
</div>