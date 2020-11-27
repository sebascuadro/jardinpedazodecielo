<?php
/** Template version: 3.0.0
 *
 * -= 3.0.0 =-
 * Initial template
 *
 */ ?>

<?php /** @var CUAR_SearchPageAddOn $this */ ?>

<?php $this->print_form_header(); ?>

<div class="form-group mb-none">
    <div class="control-container">
        <div class="input-group input-hero input-hero-sm">
                <span class="input-group-addon">
                  <i class="fa fa-search"></i>
                </span>
            <input type="text" name="cuar_query" id="cuar_query" class="form-control" placeholder="<?php esc_attr_e('Search something ...', 'cuarse'); ?>" value="<?php echo $this->criteria['query']; ?>"/>
        </div>
    </div>
</div>

<div class="form-group hidden">
    <div class="submit-container">
        <input type="submit" name="cuar_do_search" value="<?php _e("Search", 'cuarse'); ?>" class="btn btn-primary pull-right"/>
    </div>
</div>

<?php $this->print_form_footer(); ?>