/*
 * 	Script for tweaking WP editor behaviors
 *  By Thomas Lartaud
 *  Released under GPL License
 */
(function ($)
{
    $(document).ready(function ()
    {
        var $body = $('body');
        if ($body.hasClass('post-type-cuar_conversation')
                || $body.hasClass('post-type-cuar_private_file')
                || $body.hasClass('post-type-cuar_private_page')
                || $body.hasClass('post-type-cuar_project')) {

            $(document).on('tinymce-editor-init', function (event, editor)
            {
                editor.on('focus', function (e)
                {
                    // Make sure the editor is containing needed WPCA div (required by Summernote on frontend)
                    var text = editor.getContent();
                    if (!text.startsWith('<div>')) {
                        editor.setContent('<div>' + text + '</div>');
                    }
                });
            });
        }
    });
})(jQuery);
