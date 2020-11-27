<?php /** Template version: 3.0.0
 *
 * -= 3.0.0 =-
 * - Updated markup for the new master-skin UI
 *
 * -= 1.2.1 =-
 * Fix spelling of criteria
 *
 * -= 1.2.0 =-
 * Removed deprecated function call (get_private_post_types() -> get_content_post_types())
 *
 * -= 1.1.0 =-
 * Added some actions
 * Corrected some invalid HTML attributes
 *
 * -= 1.0.0 =-
 * Initial template
 */ ?>

<?php /** @var CUAR_SearchPageAddOn $this */ ?>

<?php $this->print_form_header(); ?>

<?php do_action('cuar/search/before_form_fields'); ?>

    <div class="form-group">
        <label for="pwd" class="control-label"><?php _e('Query', 'cuarse'); ?></label>

        <div class="control-container">
            <input type="text" name="cuar_query" id="cuar_query" class="form-control" value="<?php echo $this->criteria['query']; ?>"/>
        </div>
    </div>

    <div class="form-group">
        <label for="cuar_post_type" class="control-label"><?php _e('Content type', 'cuarse'); ?></label>

        <div class="control-container">
            <select name="cuar_post_type" id="cuar_post_type" class="form-control">
                <option value="any" <?php selected('any', $this->criteria['post_type']); ?>><?php _e('Any type', 'cuarse'); ?></option>

                <?php
                $content_types = $this->plugin->get_content_post_types();
                $container_types = $this->plugin->get_container_post_types();
                $types = array_merge($content_types, $container_types);
                foreach ($types as $post_type) :
                    ?>
                    <option value="<?php echo $post_type; ?>" <?php selected($post_type, $this->criteria['post_type']); ?>><?php echo $this->get_post_type_label($post_type); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="cuar_sort_by" class="control-label"><?php _e('Sort by', 'cuarse'); ?></label>

        <div class="control-container">
            <select name="cuar_sort_by" id="cuar_sort_by" class="form-control">
                <option value="date" <?php selected('date', $this->criteria['sort_by']); ?>><?php _e('Publishing date', 'cuarse'); ?></option>
                <option value="modified" <?php selected('modified', $this->criteria['sort_by']); ?>><?php _e('Last modification', 'cuarse'); ?></option>
                <option value="title" <?php selected('title', $this->criteria['sort_by']); ?>><?php _e('Title', 'cuarse'); ?></option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="cuar_sort_order" class="control-label"><?php _e('Sort order', 'cuarse'); ?></label>

        <div class="control-container">
            <select name="cuar_sort_order" id="cuar_sort_order" class="form-control">
                <option value="ASC" <?php selected('ASC', $this->criteria['sort_order']); ?>><?php _e('Ascending', 'cuarse'); ?></option>
                <option value="DESC" <?php selected('DESC', $this->criteria['sort_order']); ?>><?php _e('Descending', 'cuarse'); ?></option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="cuar_limit" class="control-label"><?php _e('Max. results', 'cuarse'); ?></label>

        <div class="control-container">
            <select name="cuar_limit" id="cuar_limit" class="form-control">
                <option value="10" <?php selected('10', $this->criteria['limit']); ?>>10</option>
                <option value="25" <?php selected('25', $this->criteria['limit']); ?>>25</option>
                <option value="50" <?php selected('50', $this->criteria['limit']); ?>>50</option>
            </select>
        </div>
    </div>

<?php do_action('cuar/search/after_form_fields'); ?>


<?php do_action('cuar/search/before_submit_button'); ?>

    <div class="form-group">
        <div class="submit-container">
            <input type="submit" name="cuar_do_search" value="<?php _e("Search", 'cuarse'); ?>" class="btn btn-primary pull-right"/>
        </div>
    </div>

<?php $this->print_form_footer(); ?>