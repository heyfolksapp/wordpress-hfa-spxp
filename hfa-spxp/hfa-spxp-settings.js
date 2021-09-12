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
jQuery(document).ready( function($) {
    jQuery('input#hfaspxp-select-profile-image').click(function(e) {
        e.preventDefault();
        var image_frame;
        if(image_frame){
            image_frame.open();
        }
        // Define image_frame as wp.media object
        image_frame = wp.media({
            title: 'Select Profile Photo',
            multiple : false,
            library : {
                type : 'image',
            }
        });
        image_frame.on('close',function() {
            // On close, get selections and save to the hidden input
            // plus other AJAX stuff to refresh the image preview
            var selection =  image_frame.state().get('selection');
            var gallery_ids = new Array();
            var my_index = 0;
            selection.each(function(attachment) {
                gallery_ids[my_index] = attachment['id'];
                my_index++;
            });
            var ids = gallery_ids.join(",");
            if(ids != '' && ids != jQuery('input#profile-image-id').val()) {
                jQuery('input#profile-image-id').val(ids);
                Refresh_Image(ids);
            }
        });
        image_frame.on('open',function() {
            // On open, get the id from the hidden input
            // and select the appropiate images in the media manager
            var selection =  image_frame.state().get('selection');
            var ids = jQuery('input#profile-image-id').val().split(',');
            ids.forEach(function(id) {
                var attachment = wp.media.attachment(id);
                attachment.fetch();
                selection.add( attachment ? [ attachment ] : [] );
            });
        });
        image_frame.open();
    });
    jQuery('a#hfaspxp-remove-profile-image').click(function(e) {
        e.preventDefault();
        jQuery('a#hfaspxp-remove-profile-image').hide();
        jQuery('input#profile-image-id').val('');
        Refresh_Image('');
    });
});

function Refresh_Image(id) {
    var data = {
        action: 'hfaspxp_get_image',
        id: id
    };
    jQuery.get(ajaxurl, data, function(response) {
        if(response.success === true) {
            jQuery('#hfaspxp-profile-image').replaceWith( response.data.image );
            if(id != '') {
                jQuery('a#hfaspxp-remove-profile-image').show();
            }
        }
    });
}