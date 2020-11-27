(function ($)
{
    "use strict";

    $(document).ready(function ()
    {
        // Init Select2 - Basic Single
        $(".cuarsu-js-id-select").each(function ()
        {
            var nonce = $(this).data('nonce');
            var body = $('body');
            $(this).select2({
                width: '100%',
                dropdownParent: body.hasClass('wp-admin') ? body : $('.cuarsu-js-id-select-container'),
                ajax          : {
                    url           : cuar.ajaxUrl,
                    dataType      : 'json',
                    data          : function (params)
                    {
                        return {
                            search: params.term,
                            nonce : nonce,
                            action: 'cuar_search_fake_identity',
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