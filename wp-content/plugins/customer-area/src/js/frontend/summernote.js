function bootstrapSummernote($, editorSelector) {
    "use strict";

    // Bail if summernote is not loaded
    // ------------------------------------------------------------------------------------------------------------------ //
    if (!$.isFunction($.fn.summernote)) {
        console.log('WPCA error: Summernote dependency missing.');
        return;
    }

    // Summernote Helper: JS error handler
    // ------------------------------------------------------------------------------------------------------------------ //
    function jsError(string) {
        $(editorSelector + ' + .note-editor > .note-toolbar > .cuar-js-manager-errors').hide().empty().append(
            '<div class="alert alert-danger alert-dismissable cuar-js-error-item mbn mt-xs" style="margin-right: 5px; line-height: 1.2em;">' +
            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
            '<span class="cuar-js-error-content" style="font-weight: lighter!important;">' + string + '</span>' +
            '</div>').show();
    }

    // Summernote Plugin: Remove image button
    // ------------------------------------------------------------------------------------------------------------------ //
    (function (factory) {
        if (typeof define === 'function' && define.amd) {
            define(['jquery'], factory);
        } else if (typeof module === 'object' && module.exports) {
            module.exports = factory(require('jquery'));
        } else {
            factory(window.jQuery);
        }
    }(function ($) {
        $.extend(true, $.summernote.lang, {
            'en-US': {
                deleteImage: {
                    tooltip: cuar.ajaxEditorDeleteImg
                }
            }
        });
        $.extend($.summernote.options, {
            deleteImage: {
                icon: '<i class="note-icon-trash"></i>'
            }
        });
        $.extend($.summernote.plugins, {
            'deleteImage': function (context) {
                var ui = $.summernote.ui,
                    $note = context.layoutInfo.note,
                    $editor = context.layoutInfo.editor,
                    $editable = context.layoutInfo.editable,
                    options = context.options,
                    lang = options.langInfo;
                context.memo('button.deleteImage', function () {
                    var button = ui.button({
                        contents: options.deleteImage.icon,
                        tooltip: lang.deleteImage.tooltip,
                        click: function () {
                            var img = $($editable.data('target'));
                            updateImage(img, 'delete', function () {
                                context.invoke('editor.afterCommand');
                            });
                        }
                    });
                    return button.render();
                });
            }
        });
    }));

    function updateImage(file, method, callback) {
        if (method === 'upload' && !file.type.includes('image')) {
            return jsError(cuar.ajaxEditorImageIsNotImg);
        }

        var data = new FormData(),
            nonce = $("#cuar_insert_image_nonce").val(),
            type = $("#cuar_post_type").val(),
            id = $("#cuar_post_id").val();

        data.append("nonce", nonce);
        data.append("post_type", type);
        data.append("post_id", id);

        if (method === 'upload') {
            data.append('action', 'cuar_insert_image');
            data.append('file', file);

        } else if (method === 'delete') {
            data.append("action", 'cuar_delete_image');
            data.append("name", file.data('filename'));
            data.append("subdir", file.data('subdir'));
            data.append("author", file.data('author'));
            data.append("hash", file.data('hash'));
        }

        $.ajax({
            url: cuar.ajaxUrl,
            type: 'POST',
            contentType: false,
            cache: false,
            processData: false,
            dataType: 'JSON',
            data: data,
            success: function (response, textStatus, jqXHR) {
                if (response.success === true) {
                    if (method === 'upload') {
                        $(editorSelector).summernote('insertImage', response.data.url, function ($image) {
                            $image.css('width', 'auto');
                            $image.css('height', 'auto');
                            $image.css('max-width', '100%');
                            $image.attr('data-subdir', response.data.subdir);
                            $image.attr('data-filename', response.data.name);
                            $image.attr('data-author', response.data.author);
                            $image.attr('data-hash', response.data.hash);
                        });
                    } else if (method === 'delete') {
                        if (file.parent().is('a')) {
                            file.parent().remove();
                        } else {
                            file.remove();
                        }
                        if (typeof callback === 'function') {
                            callback();
                        }
                    }
                } else {
                    return jsError(response.data);
                }
            }
        }).fail(function (e) {
            return jsError(cuar.ajaxEditorServerUnreachable);
        });
    }

    // Summernote Plugin: Soft breaks only
    // ------------------------------------------------------------------------------------------------------------------ //

    // Allow Summernote to not auto-generate p tags
    $.summernote.dom.emptyPara = "<div>" + "\n" + "</div>";

    // Initiate plugin
    $.extend($.summernote.plugins, {
        'brenter': function (context) {
            var self = this,
                ui = $.summernote.ui,
                $note = context.layoutInfo.note,
                $editor = context.layoutInfo.editor,
                options = context.options,
                lang = options.langInfo;

            this.events = {
                // Bind on ENTER
                'summernote.enter': function (we, e) {
                    e.preventDefault(); // Prevent <div> creation
                    $editor.find('.note-editable > div').append('<br>'); // Add a <br> at the end of your text because if you don't the first enter won't do a new line.
                    context.invoke('editor.pasteHTML', '\n');
                    $editor.find('.note-editable > div > br + br:last-child').remove(); // Remove extra added <br> but keep last one.
                },
                // Always activate SHIFT key on ENTER
                'insertParagraph': function (evt) {
                    if (evt.which === 13 || evt.keyCode === 13)
                        evt.shiftKey = true;
                },

                // Do not allow div wrapper to be removed
                'summernote.codeview.toggled': function (we, e) {
                    var isCodeView = context.invoke('codeview.isActivated');
                    if (!isCodeView) {
                        if (!$(this).val().startsWith('<div>')) {
                            $editor.find('.note-editable').wrapInner("<div></div>");
                        }
                    }
                }
            };
        }
    });

    // Summernote Plugin: Paste cleanup
    // ------------------------------------------------------------------------------------------------------------------ //
    /* https://github.com/DiemenDesign/summernote-cleaner */
    (function (factory) {
        if (typeof define === 'function' && define.amd) {
            define(['jquery'], factory);
        } else if (typeof module === 'object' && module.exports) {
            module.exports = factory(require('jquery'));
        } else {
            factory(window.jQuery);
        }
    }
    (function ($) {
        $.extend($.summernote.options, {
            cleaner: {
                action: 'both', // both|button|paste 'button' only cleans via toolbar button, 'paste' only clean when pasting content, both does both options.
                newline: '<br>', // Summernote's default is to use '<p><br></p>'
                notStyle: 'position:absolute;top:0;left:0;right:0',
                icon: '<i class="note-icon"><svg xmlns="http://www.w3.org/2000/svg" id="libre-paintbrush" viewBox="0 0 14 14" width="14" height="14"><path d="m 11.821425,1 q 0.46875,0 0.82031,0.311384 0.35157,0.311384 0.35157,0.780134 0,0.421875 -0.30134,1.01116 -2.22322,4.212054 -3.11384,5.035715 -0.64956,0.609375 -1.45982,0.609375 -0.84375,0 -1.44978,-0.61942 -0.60603,-0.61942 -0.60603,-1.469866 0,-0.857143 0.61608,-1.419643 l 4.27232,-3.877232 Q 11.345985,1 11.821425,1 z m -6.08705,6.924107 q 0.26116,0.508928 0.71317,0.870536 0.45201,0.361607 1.00781,0.508928 l 0.007,0.475447 q 0.0268,1.426339 -0.86719,2.32366 Q 5.700895,13 4.261155,13 q -0.82366,0 -1.45982,-0.311384 -0.63616,-0.311384 -1.0212,-0.853795 -0.38505,-0.54241 -0.57924,-1.225446 -0.1942,-0.683036 -0.1942,-1.473214 0.0469,0.03348 0.27455,0.200893 0.22768,0.16741 0.41518,0.29799 0.1875,0.130581 0.39509,0.24442 0.20759,0.113839 0.30804,0.113839 0.27455,0 0.3683,-0.247767 0.16741,-0.441965 0.38505,-0.753349 0.21763,-0.311383 0.4654,-0.508928 0.24776,-0.197545 0.58928,-0.31808 0.34152,-0.120536 0.68974,-0.170759 0.34821,-0.05022 0.83705,-0.07031 z"/></svg></i>',
                keepHtml: true, //Remove all Html formats
                keepOnlyTags: [], // If keepHtml is true, remove all tags except these
                keepClasses: false, //Remove Classes
                badTags: ['style', 'script', 'applet', 'embed', 'noframes', 'noscript', 'html'], //Remove full tags with contents
                badAttributes: ['style', 'start'], //Remove attributes from remaining tags
                limitChars: 520, // 0|# 0 disables option
                limitDisplay: 'both', // none|text|html|both
                limitStop: false // true/false
            }
        });
        $.extend($.summernote.plugins, {
            'cleaner': function (context) {
                var self = this,
                    ui = $.summernote.ui,
                    $note = context.layoutInfo.note,
                    $editor = context.layoutInfo.editor,
                    options = context.options,
                    lang = options.langInfo;
                var cleanText = function (txt, nlO) {
                    var out = txt;
                    var isCodeView = context.invoke('codeview.isActivated');
                    if (isCodeView) {
                        return out;
                    }

                    if (!options.cleaner.keepClasses) {
                        var sS = /(class=(")?Mso[a-zA-Z]+(")?)/g;
                        out = txt.replace(sS, ' ');
                    }

                    var nL = /(\n)+/g;
                    out = out.replace(nL, nlO);

                    if (options.cleaner.keepHtml) {
                        var cS = new RegExp('<!--(.*?)-->', 'gi');
                        out = out.replace(cS, '');

                        var tS = new RegExp('<(/)*(meta|link|\\?xml:|st1:|o:|font)(.*?)>', 'gi');
                        out = out.replace(tS, '');
                        var bT = options.cleaner.badTags;
                        for (var i = 0; i < bT.length; i++) {
                            tS = new RegExp('<' + bT[i] + '\\b.*>.*</' + bT[i] + '>', 'gi');
                            out = out.replace(tS, '');
                        }

                        var allowedTags = options.cleaner.keepOnlyTags;
                        if (typeof (allowedTags) == "undefined") allowedTags = [];
                        if (allowedTags.length > 0) {
                            allowedTags = (((allowedTags || '') + '').toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');
                            var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi;
                            out = out.replace(tags, function ($0, $1) {
                                if (allowedTags.indexOf('<' + $1.toLowerCase() + '>') > -1 && $1.toLowerCase() === 'br') {
                                    return "\n";
                                }
                                return allowedTags.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : ''
                            });
                        }

                        var bA = options.cleaner.badAttributes;
                        for (var ii = 0; ii < bA.length; ii++) {
                            //var aS=new RegExp(' ('+bA[ii]+'="(.*?)")|('+bA[ii]+'=\'(.*?)\')', 'gi');
                            var aS = new RegExp(' ' + bA[ii] + '=[\'|"](.*?)[\'|"]', 'gi');
                            out = out.replace(aS, '');

                            aS = new RegExp(' ' + bA[ii] + '[=0-9a-z]', 'gi');
                            out = out.replace(aS, '');
                        }
                    }

                    return out;
                };
                /*
                if (options.cleaner.action == 'both' || options.cleaner.action == 'button') {
                    context.memo('button.cleaner', function () {
                        var button = ui.button({
                            contents: options.cleaner.icon,
                            tooltip: lang.cleaner.tooltip,
                            container: $editor,
                            click: function () {
                                if ($note.summernote('createRange').toString())
                                    $note.summernote('pasteHTML', $note.summernote('createRange').toString());
                                else
                                    $note.summernote('code', cleanText($note.summernote('code')));
                                if ($editor.find('.note-status-output').length > 0)
                                    $editor.find('.note-status-output').html('<div class="alert alert-success">' + lang.cleaner.not + '</div>');
                                else
                                    $editor.find('.note-editing-area').append('<div class="alert alert-success" style="' + options.cleaner.notStyle + '">' + lang.cleaner.not + '</div>');
                            }
                        });
                        return button.render();
                    });
                }
                */
                this.events = {
                    'summernote.init': function () {
                        /*
                        if ($editor.find('.note-status-output').length < 1) {
                            $editor.find('.note-statusbar').prepend('<output class="note-status-output"></output>');
                            $("head").append('<style>.note-statusbar .note-status-output{display:block;padding-top:7px;width:100%;font-size:14px;line-height:1.42857143;height:25px;color:#000}.note-statusbar .pull-right{float:right!important}.note-statusbar .note-status-output .text-muted{color:#777}.note-statusbar .note-status-output .text-primary{color:#286090}.note-statusbar .note-status-output .text-success{color:#3c763d}.note-statusbar .note-status-output .text-info{color:#31708f}.note-statusbar .note-status-output .text-warning{color:#8a6d3b}.note-statusbar .note-status-output .text-danger{color:#a94442}.note-statusbar .alert{margin:-7px 0 0 0;padding:2px 10px;border:1px solid transparent;border-radius:0}.note-statusbar .alert .note-icon{margin-right:5px}.note-statusbar .alert-success{color:#3c763d!important;background-color: #dff0d8 !important;border-color:#d6e9c6}.note-statusbar .alert-info{color:#31708f;background-color:#d9edf7;border-color:#bce8f1}.note-statusbar .alert-warning{color:#8a6d3b;background-color:#fcf8e3;border-color:#faebcc}.note-statusbar .alert-danger{color:#a94442;background-color:#f2dede;border-color:#ebccd1}</style>');
                        }
                        if (options.cleaner.limitChars != 0 || options.cleaner.limitDisplay != 'none') {
                            var textLength = $editor.find(".note-editable").text().replace(/(<([^>]+)>)/ig, "").replace(/( )/, " ");
                            var codeLength = $editor.find('.note-editable').html();
                            var lengthStatus = '';
                            if (textLength.length > options.cleaner.limitChars && options.cleaner.limitChars > 0)
                                lengthStatus += 'text-danger">';
                            else
                                lengthStatus += '">';
                            if (options.cleaner.limitDisplay == 'text' || options.cleaner.limitDisplay == 'both') lengthStatus += lang.cleaner.limitText + ': ' + textLength.length;
                            if (options.cleaner.limitDisplay == 'both') lengthStatus += ' / ';
                            if (options.cleaner.limitDisplay == 'html' || options.cleaner.limitDisplay == 'both') lengthStatus += lang.cleaner.limitHTML + ': ' + codeLength.length;
                            $editor.find('.note-status-output').html('<small class="pull-right ' + lengthStatus + '&nbsp;</small>');
                        }
                        */
                    },
                    'summernote.keydown': function (we, e) {
                        /*
                        if (options.cleaner.limitChars != 0 || options.cleaner.limitDisplay != 'none') {
                            var textLength = $editor.find(".note-editable").text().replace(/(<([^>]+)>)/ig, "").replace(/( )/, " ");
                            var codeLength = $editor.find('.note-editable').html();
                            var lengthStatus = '';
                            if (options.cleaner.limitStop == true && textLength.length >= options.cleaner.limitChars) {
                                var key = e.keyCode;
                                var allowed_keys = [8, 37, 38, 39, 40, 46];
                                if ($.inArray(key, allowed_keys) != -1) {
                                    $editor.find('.cleanerLimit').removeClass('text-danger');
                                    return true;
                                } else {
                                    $editor.find('.cleanerLimit').addClass('text-danger');
                                    e.preventDefault();
                                    e.stopPropagation();
                                }
                            } else {
                                if (textLength.length > options.cleaner.limitChars && options.cleaner.limitChars > 0)
                                    lengthStatus += 'text-danger">';
                                else
                                    lengthStatus += '">';
                                if (options.cleaner.limitDisplay == 'text' || options.cleaner.limitDisplay == 'both')
                                    lengthStatus += lang.cleaner.limitText + ': ' + textLength.length;
                                if (options.cleaner.limitDisplay == 'both')
                                    lengthStatus += ' / ';
                                if (options.cleaner.limitDisplay == 'html' || options.cleaner.limitDisplay == 'both')
                                    lengthStatus += lang.cleaner.limitHTML + ': ' + codeLength.length;
                                $editor.find('.note-status-output').html('<small class="cleanerLimit pull-right ' + lengthStatus + '&nbsp;</small>');
                            }
                        }
                        */
                    },
                    'summernote.paste': function (we, e) {
                        if (options.cleaner.action == 'both' || options.cleaner.action == 'paste') {
                            var isCodeView = context.invoke('codeview.isActivated');
                            if (!isCodeView) {
                                e.preventDefault();
                                var ua = window.navigator.userAgent;
                                var msie = ua.indexOf("MSIE ");
                                msie = msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./);
                                var ffox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
                                var text = null;
                                if (msie)
                                    text = window.clipboardData.getData("Text");
                                else
                                    text = e.originalEvent.clipboardData.getData(options.cleaner.keepHtml ? 'text/html' : 'text/plain');
                                if (text) {
                                    if (msie || ffox)
                                        setTimeout(function () {
                                            $note.summernote('pasteHTML', cleanText(text, options.cleaner.newline));
                                        }, 1);
                                    else
                                        $note.summernote('pasteHTML', cleanText(text, options.cleaner.newline));
                                    /*
                                    if ($editor.find('.note-status-output').length > 0)
                                        $editor.find('.note-status-output').html('<div class="summernote-cleanerAlert alert alert-success">' + lang.cleaner.not + '</div>');
                                    else
                                        $editor.find('.note-resizebar').append('<div class="summernote-cleanerAlert alert alert-success" style="' + options.cleaner.notStyle + '">' + lang.cleaner.not + '</div>');
                                     */
                                }
                            }
                        }
                    }
                }
            }
        });
        /*
        $.extend(true, $.summernote.lang, {
            'en-US': {
                cleaner: {
                    tooltip: 'Cleaner',
                    not: 'Text has been Cleaned!!!',
                    limitText: 'Text',
                    limitHTML: 'HTML'
                }
            }
        });
        */
    }));

    // Summernote Options
    // ------------------------------------------------------------------------------------------------------------------ //
    var snOptions = {
        container: '#cuar-js-content-container',
        toolbar: [
            ['block', ['style']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['cleaner', ['cleaner']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['table', 'hr', 'picture', 'link']],
            ['view', ['codeview', 'fullscreen']],
            ['tools', ['undo', 'redo', 'help']]
        ],
        popover: {
            image: [
                ['custom', ['imageAttributes']],
                ['imagesize', ['imageSize100', 'imageSize50', 'imageSize25']],
                ['float', ['floatLeft', 'floatRight', 'floatNone']],
                ['custom', ['deleteImage']]
            ]
        },
        imageAttributes: {
            icon: '<i class="note-icon-pencil"/>',
            removeEmpty: false,
            disableUpload: true
        },
        cleaner: {
            action: 'paste', // both|button|paste 'button' only cleans via toolbar button, 'paste' only clean when pasting content, both does both options.
            newline: "\n", // Summernote's default is to use '<p><br></p>'
            notStyle: 'position:absolute;top:0;left:0;right:0',
            icon: '<i class="fa fa-pencil"/>',
            keepHtml: true, //Remove all Html formats
            keepOnlyTags: ['<br>'], // If keepHtml is true, remove all tags except these
            keepClasses: false, //Remove Classes
            badTags: ['iframe', 'frame', 'script', 'link', 'meta', 'applet', 'bgsound', 'embed', 'embed', 'noframes', 'noscript'], //Remove full tags with contents
            badAttributes: ['style', 'start'], //Remove attributes from remaining tags
            limitChars: 0, // 0|# 0 disables option
            limitDisplay: 'none', // none|text|html|both
            limitStop: false // true/false
        },
        callbacks: {
            onInit: function () {
                $(editorSelector + ' + .note-editor > .note-toolbar').append('<div class="cuar-js-manager-errors" style="display: none;"></div>');

                if ($.trim($(editorSelector + ' + .note-editor').find('.note-editable').html()) !== '' && !$(editorSelector + ' + .note-editor').find('.note-editable').html().startsWith("<div>")) {
                    $(editorSelector + ' + .note-editor').find('.note-editable').wrapInner("<div></div>");
                }
            },
            onImageUpload: function (files) {
                for (var i = 0; i < files.length; i++) {
                    updateImage(files[i], 'upload');
                }
            }
        }
    };

    // Setting up Locale
    if (typeof cuar !== 'undefined') {
        snOptions.lang = cuar.locale;
    }

    // Initialize Summernote
    $(editorSelector).summernote(snOptions);

    // Remove AutoLink feature
    $(editorSelector).summernote("removeModule", "autoLink");

    // Remove AutoLink on IE9
    // @see https://stackoverflow.com/questions/7556007/avoid-transformation-text-to-link-ie-contenteditable-mode
    document.execCommand("AutoUrlDetect", false, false);
}

jQuery(document).ready(function ($) {
    "use strict";

    if ($('.cuar-form .cuar-js-wizard-section').length > 0) {
        $('#cuar-js-content-container').on('cuar:wizard:initialized', function () {
            bootstrapSummernote($, ".cuar-wizard .cuar-js-richeditor");
        });
    } else {
        bootstrapSummernote($, ".cuar-js-richeditor");
    }
});



