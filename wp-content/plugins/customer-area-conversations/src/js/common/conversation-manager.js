/*
 * 	Scripts for the private files core add-on
 *  By Vincent Mimoun-Prat / MarvinLabs (www.marvinlabs.com)
 *  Released under GPL License
 */
(function ($) {
    if (!$.cuar) {
        $.cuar = {};
    }

    $.cuar.conversationManager = function (el, options) {

        var base = this;

        // Access to jQuery and DOM versions of element
        base.$el = $(el);
        base.el = el;

        // Add a reverse reference to the DOM object
        base.$el.data("cuar.conversationManager", base);

        // Remember the editor
        base.richEditorType = null;

        /**
         * Initialisation
         */
        base.init = function () {
            // Merge default options
            base.options = $.extend({}, $.cuar.conversationManager.defaultOptions, options);

            var replyList = base._getReplyList();

            // Errors
            base._getErrorList().on('click', '.cuar-js-dismiss', base._onDismissError);

            base._detectRichEditorType();

            // Manager actions
            if (base._isTinyMceEnabled()) {
                tinymce.on('AddEditor', function (e) {
                    tinymce.get(base.options.tinyMceEditor).on('keyup', function (ed, e) {
                        base._onAddReplyContentChanged(base._getAddReplyContent());
                    });
                });
            } else if (base._isSummerNoteEnabled()) {
                $(base.options.replyContentContainer, base.el)
                    .find('.cuar-js-richeditor')
                    .on('summernote.change', function (we, contents, $editable) {
                        base._onAddReplyContentChanged(contents);
                    });
            } else {
                $(base.options.replyContentContainer, base.el)
                    .find('textarea')
                    .keyup(function (event) {
                        base._onAddReplyContentChanged(base._getAddReplyContent());
                    });
            }

            base.$el.on('click', base.options.addButton, base._addReply);

            // Reply actions
            replyList.on('click', base.options.deleteButton, base._deleteReply);
        };

        /**
         * Rich editor detection
         * @private
         */
        base._detectRichEditorType = function () {
            if ($(base.options.replyContentContainer, base.el).find('.tmce-active').length > 0) {
                base.richEditorType = 'tinymce';
            } else if ($(base.options.replyContentContainer, base.el).find('textarea').hasClass('cuar-js-richeditor')) {
                base.richEditorType = 'summernote';
            } else {
                base.richEditorType = null;
            }
        };

        /**
         * Submit the form when enter key is pressed
         * @param content
         * @private
         */
        base._onAddReplyContentChanged = function (content) {
            base._updateAddReplyButtonState(content);
        };

        //noinspection JSUnusedLocalSymbols
        /**
         * Enable/disable the add reply button
         * @param content
         * @private
         */
        base._updateAddReplyButtonState = function (content) {
            if (!base._canAddReply(content)) {
                base._getAddReplyButton().attr('disabled', 'disabled');
            } else {
                base._getAddReplyButton().removeAttr('disabled');
            }
        };

        /**
         * Return true if the reply can really be added
         * @returns {boolean}
         * @private
         */
        base._canAddReply = function (content) {
            return content.length > 0;
        };

        /**
         * Callback for the button which adds a reply
         *
         * @param event
         * @private
         */
        base._addReply = function (event) {
            event.preventDefault();

            base._getShownWhenLoading().slideDown();
            base._disableAddForm();

            var ajaxParams = {
                'action': 'cuar_add_reply',
                'cuar_add_reply_nonce': base._getAddReplyNonce(),
                'cuar_conversation_id': base._getConversationId(),
                'cuar_reply_content': base._getAddReplyContent()
            };

            $.post(
                cuar.ajaxUrl,
                ajaxParams,
                function (response) {
                    base._getShownWhenLoading().slideUp();
                    base._enableAddForm();

                    if (response.success == false) {
                        base._showError(response.data);
                        return;
                    }

                    base._showError(null);
                    base._resetAddReplyForm();
                    base._getEmptyMessage().hide();

                    base._onReplyAdded(response.data);
                }
            );
        };

        /**
         * Create the HTML element for the new reply that just got posted with AJAX
         * @param data
         * @private
         */
        base._onReplyAdded = function (data) {
            var item = base._getReplyTemplate().clone();

            var templateClass = base.options.replyTemplateItem;
            if (templateClass.charAt(0) == '.') templateClass = templateClass.substr(1);
            item.removeClass(templateClass);

            item.attr("id", "cuar_reply_" + data.reply_id);
            item.attr("style", "");
            item.data('reply-id', data.reply_id);

            item.find(base.options.replyContent).html(data.reply_content);
            item.find(base.options.replyTimestamp).html(data.reply_date + ' - ' + data.reply_time);
            item.find(base.options.replyAuthorName).html(data.author_name);
            item.find(base.options.replyAvatarUrl).attr('src', data.author_avatar_url);

            if (!base.options.userIsConversationAuthor) {
                item.find(base.options.showWhenAuthor).remove();
            } else {
                item.find(base.options.hideWhenAuthor).remove();
            }

            if (!data.user_can_delete) {
                item.find(base.options.deleteButton).remove();
            }

            item.appendTo(base._getReplyList());
        };

        /**
         * Disable to form to add a reply
         * @private
         */
        base._disableAddForm = function () {
            base._getAddReplyForm().animate({opacity: 0.5});
            base._getAddReplyButton().attr('disabled', 'disabled');
        };

        /**
         * Enable to form to add a reply
         * @private
         */
        base._enableAddForm = function () {
            base._getAddReplyForm().animate({opacity: 1});
            base._getAddReplyButton().removeAttr('disabled');
        };

        /**
         * Show an error message
         * @param errorMessage
         * @private
         */
        base._showError = function (errorMessage) {
            if (errorMessage != null && errorMessage.length > 0) {
                base._getErrorList()
                    .show()
                    .find(base.options.errorItemContent).html(errorMessage);
            } else {
                base._getErrorList().hide();
            }
        };

        /**
         * Reset the form after a reply has been added
         * @private
         */
        base._resetAddReplyForm = function () {
            if (base._isTinyMceEnabled()) {
                var editor = tinyMCE.get(base.options.tinyMceEditor);
                editor.setContent('');
                editor.focus();
            } else if (base._isSummerNoteEnabled()) {
                //noinspection JSDuplicatedDeclaration
                var replyEditor = $(base.options.replyContentContainer, base.el).find('.cuar-js-richeditor');
                replyEditor.summernote('code', '');
                replyEditor.summernote('focus');
            } else {
                //noinspection JSDuplicatedDeclaration
                var replyEditor = $(base.options.replyContentContainer, base.el).find('textarea');
                replyEditor.val('').focus();
            }

            $(base.options.addButton, base.el).attr('disabled', 'disabled');
        };

        /**
         * Callback for the button which deletes a reply
         * @param event
         * @private
         */
        base._deleteReply = function (event) {
            event.preventDefault();

            if (!confirm(cuar.deleteReplyConfirmMessage)) return;

            var reply = base._getReplyFromAction($(this));
            base._disableReplyItem(reply);

            var ajaxParams = {
                'action': 'cuar_delete_reply',
                'cuar_delete_reply_nonce': base._getDeleteReplyNonce(),
                'cuar_conversation_id': base._getConversationId(),
                'cuar_reply_id': base._getReplyId(reply)
            };

            $.post(
                cuar.ajaxUrl,
                ajaxParams,
                function (response) {
                    if (response.success == false) {
                        alert(response.data);
                        base._enableReplyItem(reply);
                        return;
                    }

                    if (response.data.deleted) {
                        reply.slideUp(400, function () {
                            reply.remove();

                            if (base._getReplies().length == 0) {
                                base._getEmptyMessage().show();
                            } else {
                                base._getEmptyMessage().hide();
                            }
                        });
                    }
                }
            );
        };

        /**
         * Reset the form after a reply has been added
         * @private
         */
        base._disableReplyItem = function (item) {
            item.animate({opacity: 0.5});
            item.find(base.options.deleteButton).css('opacity', 0);
        };

        /**
         * Enable to form to add a reply
         * @private
         */
        base._enableReplyItem = function () {
            item.animate({opacity: 1});
            item.find(base.options.deleteButton).css('opacity', 1);
        };


        /** Getter */
        base._getReplyFromAction = function (actionElt) {
            return actionElt.closest(base.options.replyItem);
        };

        /** Getter */
        base._getReplyId = function (replyElt) {
            return replyElt.data('reply-id');
        };

        /** Getter */
        base._getReplyTemplate = function () {
            return $(base.options.replyTemplateItem, base.el);
        };

        /** Getter */
        base._getAddReplyNonce = function () {
            return $('input[name=cuar_add_reply_nonce]', base.el).val();
        };

        /** Getter */
        base._getDeleteReplyNonce = function () {
            return $('input[name=cuar_delete_reply_nonce]', base.el).val();
        };

        /** Getter */
        base._getConversationId = function () {
            return $(base.el).data('conversation-id');
        };

        /** Getter */
        base._getEmptyMessage = function () {
            return $(base.options.emptyMessage, base.el);
        };

        /** Getter */
        base._getAddReplyForm = function () {
            return $(base.options.addReplyForm, base.el);
        };

        /** Getter */
        base._getShownWhenLoading = function () {
            return $(base.options.shownWhenLoading, base.el);
        };

        /** Getter */
        base._getHiddenWhenLoading = function (reply) {
            return reply.find(base.options.hiddenWhenLoading);
        };

        /** Getter */
        base._isTinyMceEnabled = function () {
            return base.richEditorType == 'tinymce';
        };

        /** Getter */
        base._isSummerNoteEnabled = function () {
            return base.richEditorType == 'summernote';
        };

        /** Getter */
        base._getAddReplyContent = function () {
            // If rich editor is enabled, use tinyMCE or SummerNote functions
            if (base._isTinyMceEnabled()) {
                return tinyMCE.get(base.options.tinyMceEditor).getContent();
            }

            if (base._isSummerNoteEnabled()) {
                return $(base.options.replyContentContainer, base.el).find('.cuar-js-richeditor').summernote('code');
            }

            // Else, we find the text area and use its value
            return $(base.options.replyContentContainer, base.el).find('textarea').val();
        };

        /** Getter */
        base._getAddReplyButton = function () {
            return $(base.options.addButton, base.el);
        };

        /** Getter */
        base._getReplyList = function () {
            return $(base.options.replyList, base.el);
        };

        /** Getter */
        base._getReplies = function () {
            return base._getReplyList().find(base.options.replyItem);
        };

        /** Getter */
        base._getErrorList = function () {
            return $(base.options.errorList, base.el);
        };

        // Make it go!
        base.init();
    };

    $.cuar.conversationManager.defaultOptions = {
        userIsConversationAuthor: false,

        replyList: '.cuar-js-reply-list',                      // The container for the list of replies
        replyItem: '.cuar-js-reply',                           // An item from the list
        replyContentContainer: '.cuar-js-reply-content',       // Input for new reply content
        addButton: '.cuar-js-add-action',                      // Button which adds a reply
        errorList: '.cuar-js-manager-errors',                  // The container for the list of errors
        errorItemContent: '.cuar-js-error-content',                  // The container for the list of errors
        errorDismissClasses: 'fa fa-times-circle',             // CSS class to show next to error message
        tinyMceEditor: 'cuarme_reply_content',                 // ID of the tiny MCE editor
        shownWhenLoading: '.cuar-js-shown-when-loading',       // Show this when doing ajax
        hiddenWhenLoading: '.cuar-js-hidden-when-loading',     // Hide this when doing ajax
        addReplyForm: '.cuar-js-add-reply-form',               // The whole add reply form
        replyTemplateItem: '.cuar-js-reply-template',          // An item to clone from when adding
        replyContent: '.cuar-js-content',
        replyTimestamp: '.cuar-js-timestamp',
        replyAuthorName: '.cuar-js-author',
        replyAvatarUrl: '.cuar-js-avatar',
        hideWhenAuthor: '.cuar-js-hide-when-author',
        showWhenAuthor: '.cuar-js-show-when-author',
        emptyMessage: '.cuar-js-empty-message',                // The container for the empty list indicator
        deleteButton: '.cuar-js-delete-action',                // Button which deletes a reply
    };

    $.fn.conversationManager = function (options) {
        return this.each(function () {
            (new $.cuar.conversationManager(this, options));
        });
    };

})(jQuery);
