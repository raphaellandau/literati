jQuery( document ).ready( function( $ ) {

    post_search( $( ':input.wcctyp-post-search' ) );

    $( '#the-list' ).on( 'click', '.editinline', function() {
        post_search_bulk_quick_edit();
    });

    $( '#wpbody' ).on( 'click', '#doaction, #doaction2', function() {
        post_search_bulk_quick_edit();
    });

    $( '#woocommerce-product-data' ).on( 'woocommerce_variations_loaded', function() {
        post_search( $( ':input.wcctyp-post-search' ) );
    });

    function post_search_bulk_quick_edit() {

        $('.wcctyp-post-search-quick-edit.enhanced+.select2').remove();
        $('.wcctyp-post-search-quick-edit.enhanced').replaceWith( '' +
            '<select ' +
                'style="width: 100%;" ' +
                'name="custom_thank_you_page" ' +
                'class="wcctyp-post-search-quick-edit text" ' +
                'placeholder="Search a page or enter a URL" ' +
                'data-multiple="false" ' +
                'data-selected="No change" ' +
            '>' +
                '<option value="no_change">' + wcctyp.i18n.noChange + '</option>' +
            '</select>'
        );
        post_search( $( '.wcctyp-post-search-quick-edit:not(.enhanced)' ) );
    }


    function post_search( el ) {
        el.each( function () {
            var select2_args = {
                allowClear: true,
                placeholder: $( this ).attr( 'placeholder' ),
                minimumInputLength: 3,
                escapeMarkup: function (m) {
                    return m;
                },
                templateResult: function (data) {
                    if ( typeof data.type == 'undefined' ) data.type = '';

                    return '' +
                        '<span class="select2-match wcctyp-select2-match">' +
                        '<span class="select2-result-title">' + data.text + '</span>' +
                        '<span class="select2-result-type">' + data.type + '</span>' +
                        '</span>';
                },
                formatSelection: function (data) {
                    return data.text;
                },
                ajax: {
                    url: ajaxurl,
                    dataType: 'json',
                    delay: 500,
                    data: function (term) {
                        return {
                            term: term,
                            action: 'wcctyp_search_posts',
                            security: wcctyp.nonce,
                        };
                    },
                    processResults: function (data, page) {
                        return {results: data}
                    },
                    cache: true
                }
            };

            $( this ).addClass( 'enhanced' ).select2( select2_args );
        } );
    }
    window.post_search = post_search;
});
