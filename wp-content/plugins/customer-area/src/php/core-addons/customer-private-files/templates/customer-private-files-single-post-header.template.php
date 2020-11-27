<?php
/** Template version: 3.2.0
 *
 * -= 3.2.0 =-
 * - Improve UI
 * - Group owners by type roles / groups / projects / users
 * - Add links to carousel tiles that allow quick assignment of new private contents
 * - Add hooks to help others add-on to dynamically add contents to the carousel
 *
 * -= 3.1.0 =-
 * - Replace clearfix CSS classes with cuar-clearfix
 *
 * -= 3.0.0 =-
 * - Improve UI for new master-skin
 *
 * -= 1.3.0 =-
 * - Compatibility with the new multiple attached files
 * - New hooks for attachment items
 *
 * -= 1.2.0 =-
 * - Updated to new responsive markup
 *
 * -= 1.1.0 =-
 * - Added file size
 *
 * -= 1.0.0 =-
 * - Initial version
 *
 */
?>

<?php
$pf_addon = cuar_addon('customer-private-files');
$po_addon = cuar_addon('post-owner');

$post_id = get_the_ID();

$date = get_the_date();
$date_month = get_the_date('F');
$date_year = get_the_date('Y');
$date_url_year = $pf_addon->get_date_archive_url($date_year);
$date_url_month = $pf_addon->get_date_archive_url($date_year, $date_month);

$author_id = get_the_author_meta('ID');
$author_name = get_the_author_meta('display_name');
$author_email = get_the_author_meta('user_email');
$author_biography = get_the_author_meta('description');
$author_website = get_the_author_meta('user_url');
$author_avatar = get_avatar($author_email, 64);
$author_url = $pf_addon->get_author_archive_url($author_id);
$author_submenu_links = $po_addon->get_owner_submenu_links($author_id, 'usr');

$owners = cuar_get_the_owner(get_the_ID(), false);
?>

<div class="cuar-single-post-header row mb-md cuar-clearfix">
    <div class="cuar-js-slick-responsive">

        <?php do_action('cuar/private-content/view/carousel/before-author', $post_id); ?>

        <div class="cuar-author cuar-js-slick-slide-item slick-slide-item">
            <div class="panel panel-tile cuar-panel-meta-tile">
                <div class="panel-body">
                    <i class="fa fa-id-badge icon-bg"></i>
                    <div class="cuar-meta-label">
                        <span><?php _e('Author', 'cuar'); ?></span>
                    </div>
                    <div class="cuar-meta-value">
                        <div class="cuar-panel-meta-tile-sub-head mb5">
                            <?php if ($author_avatar): ?>
                                <div class="cuar-panel-meta-tile-avatar pull-left mr-xs">
                                    <?php echo $author_avatar; ?>
                                </div>
                            <?php endif; ?>

                            <span class="cuar-meta-tile-info">
                                <?php echo $author_name; ?>
                            </span>
                        </div>

                        <div class="cuar-panel-meta-tile-links">
                            <?php if ($author_website): ?>
                                <div class="cuar-author-website cuar-meta-link mr5 pull-left">
                                    <a href="<?php echo esc_url($author_website); ?>" class="btn btn-xs btn-default"
                                       target="_BLANK"
                                       title="<?php echo esc_attr(sprintf(__('Show %s\'s website', 'cuar'), $author_name)); ?>">
                                        <i class="fa fa-link pn"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="cuar-author-archives-url cuar-meta-link mr5 pull-left">
                                <a href="<?php echo esc_url($author_url); ?>" class="btn btn-xs btn-default"
                                   title="<?php echo esc_attr(sprintf(__('Show all similar contents from %s', 'cuar'), $author_name)); ?>">
                                    <i class="fa fa-th-list pn"></i>
                                </a>
                            </div>
                            <?php if ($author_submenu_links) { ?>
                                <div class="cuar-author-more cuar-js-dropdown-in-overflow cuar-meta-link mr5 pull-left">
                                    <a href="#"
                                       class="btn btn-xs btn-default dropdown-toggle"
                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-h pn"></i> <?php _e('More', 'cuar'); ?>
                                    </a>
                                    <ul class="cuar-js-dropdown-in-overflow-menu dropdown-menu">
                                        <?php foreach ($author_submenu_links as $author_link) { ?>
                                            <li>
                                                <a href="<?php echo esc_url($author_link['url']); ?>"
                                                   title="<?php echo esc_attr($author_link['tooltip']); ?>"
                                                   class="<?php echo esc_attr($author_link['extra_class']); ?>">
                                                    <?php echo $author_link['title']; ?>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php do_action('cuar/private-content/view/carousel/before-date', $post_id); ?>

        <div class="cuar-date cuar-js-slick-slide-item slick-slide-item">
            <div class="panel panel-tile cuar-panel-meta-tile">
                <div class="panel-body">
                    <i class="fa fa-calendar icon-bg"></i>
                    <div class="cuar-meta-label">
                        <span>
                            <?php _e('Date', 'cuar'); ?>
                        </span>
                    </div>
                    <div class="cuar-meta-value">
                        <div class="cuar-panel-meta-tile-sub-head mb5">
                            <span class="cuar-meta-tile-info">
                                <?php echo $date; ?>
                            </span>
                        </div>
                        <div class="cuar-panel-meta-tile-links">
                            <div class="cuar-date-month cuar-meta-link mr5 pull-left">
                                <a href="<?php echo esc_url($date_url_month); ?>" class="btn btn-xs btn-default"
                                   title="<?php echo esc_attr(sprintf(__('Show all similar contents published in %2$s %1$s', 'cuar'), $date_year, $date_month)); ?>">
                                    <i class="fa fa-calendar pn"></i> <?php _e('Month archives', 'cuar'); ?>
                                </a>
                            </div>
                            <div class="cuar-date-year cuar-meta-link mr5 pull-left">
                                <a href="<?php echo esc_url($date_url_year); ?>" class="btn btn-xs btn-default"
                                   title="<?php echo esc_attr(sprintf(__('Show all similar contents published in %1$s', 'cuar'), $date_year)); ?>">
                                    <i class="fa fa-calendar-o pn"></i> <?php _e('Year archives', 'cuar'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php do_action('cuar/private-content/view/carousel/before-owners', $post_id); ?>

        <?php
        if (empty($owners))
        { ?>
            <div class="cuar-owner cuar-owner-unknown cuar-js-slick-slide-item slick-slide-item">
                <div class="panel panel-tile cuar-panel-meta-tile">
                    <div class="panel-body">
                        <i class="fa fa-users icon-bg"></i>
                        <div class="cuar-meta-label"><?php _e('Assigned to', 'cuar'); ?></div>
                        <div class="cuar-meta-value">
                            <div class="cuar-panel-meta-tile-sub-head mb5">
                                <span class="cuar-meta-tile-info">
                                    <?php _e('0 user', 'cuar'); ?>
                                </span>
                            </div>
                            <div class="cuar-panel-meta-tile-links">
                                <div class="cuar-meta-link mr5 pull-left">
                                    <a href="#"
                                       class="btn btn-xs btn-default"
                                       title="<?php esc_attr_e('This content is not yet assigned to anyone', 'cuar'); ?>">
                                        <i class="fa fa-user pn"></i>&nbsp;<?php _e('Not yet assigned', 'cuar'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php }
        else
        {
            $grouped_tiles = $po_addon->group_tile_owners($owners);

            foreach ($grouped_tiles as $grouped_role => $tile_data)
            { ?>

                <?php
                $count_tile_owners = $po_addon->count_grouped_tile_owners($tile_data['owners']);
                if ($count_tile_owners > 0)
                { ?>

                    <?php do_action('cuar/private-content/view/carousel/before-owner?group=' . $grouped_role,
                    $post_id); ?>

                    <div class="cuar-owner cuar-owner-<?php echo esc_attr($grouped_role); ?> cuar-js-slick-slide-item
                    slick-slide-item">
                        <div class="panel panel-tile cuar-panel-meta-tile">
                            <div class="panel-body">
                                <i class="fa fa-<?php echo $tile_data['icon']; ?> icon-bg"></i>
                                <div class="cuar-meta-label"><?php _e('Assigned to', 'cuar'); ?></div>
                                <div class="cuar-meta-value">
                                    <div class="cuar-panel-meta-tile-sub-head mb5">
                                        <span class="cuar-meta-tile-info">
                                            <?php echo $tile_data['head']; ?>
                                        </span>
                                    </div>
                                    <div class="cuar-panel-meta-tile-links">
                                        <?php
                                        foreach ($tile_data['owners'] as $role => $content_owners)
                                        {
                                            foreach ($content_owners as $owner_id)
                                            {
                                                $po_addon->print_owner_link($owner_id, $role);
                                            }
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php do_action('cuar/private-content/view/carousel/after-owner?group=' . $grouped_role,
                    $post_id); ?>

                <?php } ?>

                <?php
            }
        }
        ?>

        <?php do_action('cuar/private-content/view/carousel/after-owners', $post_id); ?>

    </div>
</div>