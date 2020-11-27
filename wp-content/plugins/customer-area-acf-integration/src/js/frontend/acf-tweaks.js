/*
 * ACF frontend tweaks
 * Author: Thomas Lartaud
 *
 */
;
(function ($, window, undefined) {
    "use strict";

    // SELECT2
    // ----------------------------------

    /*
    *  document ready
    *
    *  This function will alter select2 so they can be wrapped into our container
    *
    *  @type	function
    *  @date	12/08/2019
    *  @since	4.0.1
    *
    *  @param	n/a
    *  @return	n/a
    */
    $(document).ready(function () {

        // Check
        if (typeof acf === 'undefined') {
            console.log('Warning, ACF not yet loaded - make sure ACF is activated');
            return false;
        }

        // Callback after ACF gets initialized
        acf.addAction('select2_init', function (field) {
            field.next('.select2-container').removeClass('-acf');
        });

        // Customize ACF Select2 options
        acf.addFilter('select2_args', function (options, $select, data, field, that) {
            options.dropdownParent = $('#cuar-js-content-container');

            return options;
        });

        // Init Single Select2 custom options
        if ($.isFunction($.fn.select2)) {
            $('.acf-field-select > .acf-input > select').each(function () {
                if ($(this).attr('data-ui') === "1") {
                    $(this).addClass('select2-single');
                    if ($(this).attr('data-multiple') === "0") {
                        $(this).removeAttr('data-multiple');

                        // Better to keep search field
                        // $(this).attr('data-minimum-results-for-search', 'Infinity');
                    }
                }
            });
        }
    });


    // GOOGLE MAP
    // ----------------------------------

    /*
    *  new_map
    *
    *  This function will render a Google Map onto the selected jQuery element
    *
    *  @type	function
    *  @date	12/08/2019
    *  @since	4.0.1
    *
    *  @param	$el (jQuery element)
    *  @return	n/a
    */

    function new_map($el) {

        // var
        var $markers = $el.find('.marker');


        // vars
        var args = {
            zoom: 16,
            center: new google.maps.LatLng(0, 0),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };


        // create map
        var map = new google.maps.Map($el[0], args);


        // add a markers reference
        map.markers = [];


        // add markers
        $markers.each(function () {

            add_marker($(this), map);

        });


        // center map
        center_map(map);


        // return
        return map;

    }

    /*
    *  add_marker
    *
    *  This function will add a marker to the selected Google Map
    *
    *  @type	function
    *  @date	12/08/2019
    *  @since	4.0.1
    *
    *  @param	$marker (jQuery element)
    *  @param	map (Google Map object)
    *  @return	n/a
    */

    function add_marker($marker, map) {

        // var
        var latlng = new google.maps.LatLng($marker.attr('data-lat'), $marker.attr('data-lng'));

        // create marker
        var marker = new google.maps.Marker({
            position: latlng,
            map: map
        });

        // add to array
        map.markers.push(marker);

        // if marker contains HTML, add it to an infoWindow
        if ($marker.html()) {
            // create info window
            var infowindow = new google.maps.InfoWindow({
                content: $marker.html()
            });

            // show info window when marker is clicked
            google.maps.event.addListener(marker, 'click', function () {

                infowindow.open(map, marker);

            });
        }

    }

    /*
    *  center_map
    *
    *  This function will center the map, showing all markers attached to this map
    *
    *  @type	function
    *  @date	12/08/2019
    *  @since	4.0.1
    *
    *  @param	map (Google Map object)
    *  @return	n/a
    */

    function center_map(map) {

        // vars
        var bounds = new google.maps.LatLngBounds();

        // loop through all markers and create bounds
        $.each(map.markers, function (i, marker) {

            var latlng = new google.maps.LatLng(marker.position.lat(), marker.position.lng());

            bounds.extend(latlng);

        });

        // only 1 marker?
        if (map.markers.length == 1) {
            // set center of map
            map.setCenter(bounds.getCenter());
            map.setZoom(16);
        } else {
            // fit to bounds
            map.fitBounds(bounds);
        }

    }

    /*
    *  document ready
    *
    *  This function will render each map when the document is ready (page has loaded)
    *
    *  @type	function
    *  @date	12/08/2019
    *  @since	4.0.1
    *
    *  @param	n/a
    *  @return	n/a
    */
    // global var
    var map = null;

    $(document).ready(function () {

        $('.cuar-readonly-field-map').each(function () {

            // create map
            map = new_map($(this));

        });

    });

})(jQuery, window);
