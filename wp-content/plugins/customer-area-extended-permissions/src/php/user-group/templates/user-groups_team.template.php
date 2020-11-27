<?php /**
 * Template version: 3.0.0
 * Template zone: admin
 */ ?>

<?php /** @var $roles array */ ?>

<?php
$po_addon = cuar_addon('post-owner');
?>

<div class="cuar-user-group-team">
    <div class="form-group cuar-user-group-team cuar-user-group-member cuar-js-user-group-team">
        <div class="control-container">
            <select name="user_group_team[member][]"
                    id="user_group_team_member"
                    multiple="multiple"
                    size="10"
                    class="cuar-js-user-group-team-select"
                    data-nonce="<?php echo esc_attr(wp_create_nonce('cuar_search_user_group_team')); ?>"
                    data-placeholder="<?php esc_attr_e(
                        'Select users (hint: you can also type to search a user)', 'cuarep'); ?>">

                <?php foreach ($members as $user_id) : ?>
                    <option value="<?php echo esc_attr($user_id); ?>" selected="selected"><?php
                        echo $po_addon->ajax()->get_user_display_value($user_id, 'user_group_team'); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<script type="text/javascript">
    <!--
    (function ($)
    {
        "use strict";
        $(document).ready(function ()
        {
            $('.cuar-js-user-group-team-select').each(function ()
            {
                var nonce = $(this).data('nonce');
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
                                action: 'cuar_search_user_group_team',
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