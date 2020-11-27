<?php /** Template version: 1.0.0 */ ?>

<script type="text/javascript">
    function refreshDisplayedLayoutSettings($, templateSelector) {
        var templateId = templateSelector.val();

        // Hide all settings related to templates
        $('.cuar-js-layout-setting').parents('tr').hide();

        // Show settings related to the selected template
        $('.cuar-js-layout-setting-' + templateId).parents('tr').show();
    }

    jQuery(document).ready(function ($) {
        var templateSelector = $('.cuar-js-layout-selector select');
        templateSelector.change(function () {
            refreshDisplayedLayoutSettings($, $(this));
        });
        refreshDisplayedLayoutSettings($, templateSelector);
    });
</script>