<?php /**
 * Template version: 3.0.0
 * Template zone: admin
 */ ?>

<?php /** @var $roles array */ ?>

<?php
$po_addon = cuar_addon('post-owner');
?>

<div class="cuar-managed-group-team">
    <?php foreach ($roles as $role_id => $role_desc):
        ?>
        <div class="form-group cuar-managed-group-team cuar-managed-group-<?php echo $role_id; ?> cuar-js-managed-group-team">
            <label for="managed_group_team_<?php echo esc_attr($role_id); ?>" class="control-label">
                &nbsp;<?php echo $role_desc['label_plural']; ?>
            </label>

            <div class="control-container">
                <select name="managed_group_team[<?php echo esc_attr($role_id); ?>][]"
                        id="managed_group_team_<?php echo esc_attr($role_id); ?>"
                        multiple="multiple"
                        size="10"
                        class="cuar-js-managed-group-team-select"
                        data-role="<?php echo esc_attr($role_id); ?>"
                        data-nonce="<?php echo esc_attr(
                            wp_create_nonce('cuar_search_managed_group_team_' . $role_id)); ?>"
                        data-placeholder="<?php esc_attr_e(
                            'Select users (hint: you can also type to search a user)', 'cuarmg'); ?>">

                    <?php foreach ($role_desc['users'] as $user_id) : ?>
                        <option value="<?php echo esc_attr($user_id); ?>" selected="selected"><?php
                            echo $po_addon->ajax()->get_user_display_value($user_id, 'managed_group_team'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script type="text/javascript">
    <!--
    (function ($)
    {
        "use strict";
        $(document).ready(function ()
        {
            $('.cuar-js-managed-group-team-select').each(function ()
            {
                var nonce = $(this).data('nonce');
                var role = $(this).data('role');

                $(this).select2({
                    width     : '100%',
                    allowClear: true,
                    ajax      : {
                        url           : cuar.ajaxUrl,
                        dataType      : 'json',
                        data          : function (params)
                        {
                            return {
                                search: params.term,
                                nonce : nonce,
                                action: 'cuar_search_managed_group_team',
                                role  : role,
                                page  : params.page || 1
                            };
                        },
                        processResults: function (data)
                        {
                            if (!data.success) {
                                alert(data.data);
                                return {results: []};
                            }

                            return {
                                results   : data.data.results,
                                pagination: {
                                    more: data.data.more
                                }
                            };
                        }
                    }
                });
            })
        });
    })(jQuery);
    //-->
</script>