<?php
/** Template version: 3.0.0
 *
 * -= 3.0.0 =-
 * - Updated markup for the new master-skin UI
 *
 * -= 1.2.0 =-
 * Updated to match new responsive theme
 *
 * -= 1.1.0 =-
 * Removed deprecated function call (get_private_post_types() -> get_content_post_types())
 *
 * -= 1.0.0 =-
 * Initial template
 */ ?>

<?php /** @var CUAR_SearchPageAddOn $this */ ?>
<?php /** @var array $search_result */ ?>

<div class="cuar-title mb30"><?php _e('Search result', 'cuarse'); ?></div>

<div class="cuar-search-result">
    <?php
    $private_types = $this->plugin->get_content_post_types();
    foreach ($search_result as $type => $posts) :
        $item_template = $this->get_post_type_template($type);
        ?>
        <div class="search-divider" id="search-section-<?php echo $type; ?>">
            <span class="search-divider-content"><?php echo $this->get_post_type_label($type) ?> <span class="badge badge-primary"><?php echo count($posts); ?></span></span>
        </div>

        <div class="cuar-item-list">
            <?php foreach ($posts as $res) {
                if (get_post_type($res) != $type) continue;

                global $post;
                $post = $res;
                setup_postdata($post);

                include($item_template);
            }
            ?>
        </div>

        <?php
    endforeach; ?>
</div>