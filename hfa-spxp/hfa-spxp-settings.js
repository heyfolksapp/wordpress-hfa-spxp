/*
HFA-SPXP is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

HFA-SPXP is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with HFA-SPXP. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/
jQuery( document ).ready( function ( $ ) {

    function refreshImage( id ) {
        $.get( ajaxurl, {
            action:      'hfaspxp_get_image',
            id:          id,
            _ajax_nonce: hfaspxp.nonce,
        } ).done( function ( response ) {
            if ( response.success ) {
                $( '#hfaspxp-profile-image' ).replaceWith( response.data.image );
                $( '#hfaspxp-remove-profile-image' ).toggle( id !== '' );
            }
        } );
    }

    $( '#hfaspxp-select-profile-image' ).on( 'click', function ( e ) {
        e.preventDefault();

        const frame = wp.media( {
            title:    'Select Profile Photo',
            multiple: false,
            library:  { type: 'image' },
        } );

        frame.on( 'close', function () {
            const attachment = frame.state().get( 'selection' ).first();
            if ( ! attachment ) {
                return;
            }
            const id = String( attachment.id );
            if ( id !== $( '#profile-image-id' ).val() ) {
                $( '#profile-image-id' ).val( id );
                refreshImage( id );
            }
        } );

        frame.on( 'open', function () {
            const id = $( '#profile-image-id' ).val();
            if ( id ) {
                const attachment = wp.media.attachment( id );
                attachment.fetch();
                frame.state().get( 'selection' ).add( [ attachment ] );
            }
        } );

        frame.open();
    } );

    $( '#hfaspxp-remove-profile-image' ).on( 'click', function ( e ) {
        e.preventDefault();
        $( '#profile-image-id' ).val( '' );
        refreshImage( '' );
    } );

} );
