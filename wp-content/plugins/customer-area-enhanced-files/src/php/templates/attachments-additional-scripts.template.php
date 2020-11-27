<script type="text/javascript">
    <!--
    (function ($) {
        "use strict";
        $(document).ready(function ($) {
            if (cuar.isAdmin) {
                // Init jQuery.editable
                cuarInitEditablePlugin();
            } else {
                // Wait for wizard to be initialized before jQuery.editable init
                $('#cuar-js-content-container').on('cuar:wizard:initialized', cuarInitEditablePlugin);
            }

            function cuarInitEditablePlugin() {
                var editableCallback = function (caption, settings) {
                    var item = $(this).closest('.cuar-js-file-attachment');
                    var nonceValue = $(this).closest('.cuar-js-file-attachments').children('input[name=cuar_update_attachment_nonce]').val();

                    $(document).trigger('cuar:attachmentManager:updateFile', [
                        item,
                        item.data('post-id'),
                        nonceValue,
                        item.data('filename'),
                        caption
                    ]);

                    return caption;
                };

                var editableOptions = {
                    indicator: cuar.jeditableIndicator,
                    tooltip: cuar.jeditableTooltip,
                    cssclass: "cuar-editable-caption"
                };

                $('.cuar-js-file-attachments .cuar-js-file-attachment .cuar-js-caption').editable(editableCallback, editableOptions);

                $(document).on('cuar:attachmentManager:itemStateUpdated', function (event, item, state) {
                    if (state=='success') {
                        item.find('.cuar-js-caption').editable(editableCallback, editableOptions);
                    }
                });
            }

        });
    })(jQuery);
    // -->
</script>