<?php
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

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class HeyFolksApp_SPXP_Settings {

    const OPTIONS_PAGE_SLUG = 'hfa-spxp';

    public $options;

    public function add_hooks() {
        add_filter( 'plugin_action_links', [ $this, 'plugin_action_links' ], 10, 2 );
        add_action( 'admin_init', [ $this, 'init' ] );
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        add_action( 'wp_ajax_hfaspxp_get_image', [ $this , 'ajax_get_image' ] );
    }

    public function plugin_action_links( $actions, $plugin_file ) {
        if ( $plugin_file == 'hfa-spxp/hfa-spxp.php' ) {
            $settings = '<a href="' . admin_url( 'options-general.php?page=' . self::OPTIONS_PAGE_SLUG ) . '">' . esc_html__( 'Settings', 'hfa-spxp' ) . '</a>';
            array_unshift( $actions, $settings );
        }
        return $actions;
    }

    public function init() {
        add_settings_section(
            'hfa-spxp-settings-main',
            null,
            null,
            'hfa-spxp-settings'
        );
        add_settings_field(
            'hfa_spxp_main_profileUri',
            __( 'Profile Uri', 'hfa-spxp' ),
            [ $this, 'render_main_profileUri' ],
            'hfa-spxp-settings',
            'hfa-spxp-settings-main',
            array('label_for' => 'hfa_spxp_main_profileUri')
        );
        add_settings_section(
            'hfa-spxp-settings-profile',
            'Profile',
            null,
            'hfa-spxp-settings'
        );
        add_settings_field(
            'hfa_spxp_profile_name',
            __( 'Name', 'hfa-spxp' ),
            [ $this, 'render_profile_name' ],
            'hfa-spxp-settings',
            'hfa-spxp-settings-profile',
            array('label_for' => 'hfa_spxp_profile_name')
        );
        add_settings_field(
            'hfa_spxp_profile_shortInfo',
            __( 'Short Info', 'hfa-spxp' ),
            [ $this, 'render_profile_shortInfo' ],
            'hfa-spxp-settings',
            'hfa-spxp-settings-profile',
            array('label_for' => 'hfa_spxp_profile_shortInfo')
        );
        add_settings_field(
            'hfa_spxp_profile_image',
            __( 'Photo', 'hfa-spxp' ),
            [ $this, 'render_profile_image' ],
            'hfa-spxp-settings',
            'hfa-spxp-settings-profile',
            array('label_for' => 'hfa_spxp_profile_image')
        );
        add_settings_field(
            'hfa_spxp_about',
            __( 'About', 'hfa-spxp' ),
            [ $this, 'render_profile_about' ],
            'hfa-spxp-settings',
            'hfa-spxp-settings-profile',
            array('label_for' => 'hfa_spxp_about')
        );
        add_settings_section(
            'hfa-spxp-settings-posts',
            'Posts',
            null,
            'hfa-spxp-settings'
        );
        add_settings_field(
            'hfa_spxp_post_type',
            __( 'Post Type', 'hfa-spxp' ),
            [ $this, 'render_post_type' ],
            'hfa-spxp-settings',
            'hfa-spxp-settings-posts',
            array('label_for' => 'hfa_spxp_post_type')
        );
        add_settings_field(
            'hfa_spxp_preview_image_size',
            __( 'Preview Image Size', 'hfa-spxp' ),
            [ $this, 'render_post_preview_image_size' ],
            'hfa-spxp-settings',
            'hfa-spxp-settings-posts',
            array('label_for' => 'hfa_spxp_preview_image_size')
        );
        add_settings_field(
            'hfa_spxp_full_image_size',
            __( 'Full Image Size', 'hfa-spxp' ),
            [ $this, 'render_post_full_image_size' ],
            'hfa-spxp-settings',
            'hfa-spxp-settings-posts',
            array('label_for' => 'hfa_spxp_full_image_size')
        );
        $this->options = get_option( 'hfa-spxp', );
    }

    public function add_admin_menu() {
        add_options_page(
            'Social Profile Exchange Protocol Settings', // Page title
            'SPXP', // Menu title
            'manage_options', // Capabilities
            self::OPTIONS_PAGE_SLUG, // Menu slug
            [ $this, 'render_options_page' ] // Callback
        );
    }

    public function enqueue_scripts( $page ) {
        if( $page == 'settings_page_hfa-spxp' ) {
            wp_enqueue_media();
            wp_enqueue_script( 'hfa-spxp_script', plugins_url( '/hfa-spxp-settings.js' , __FILE__ ), array('jquery'), '0.1' );
        }
    }

    public function ajax_get_image() {
        if(isset($_GET['id']) ){
            $image = $this->get_image_html( filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT ) );
            $data = array(
                'image'    => $image,
            );
            wp_send_json_success( $data );
        } else {
            wp_send_json_error();
        }
    }

    public function render_options_page() {
        ?>
        <div class="wrap">
            <h1><?php _e( 'Social Profile Exchange Protocol Settings', 'hfa-spxp' ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'hfa-spxp-settings' );
                do_settings_sections( 'hfa-spxp-settings' );
                ?>
                <p><?php submit_button( 'Save Changes', 'button-primary', 'submit', false ); ?></p>
            </form>
        </div>
        <?php
    }

    function render_main_profileUri() {
        echo '<b>' . esc_html( get_option( 'siteurl' ) . '/spxp' ) . '</b>';
        ?>
        <p class="description">
            <?php printf( __( 'Share this <i>Profile Uri</i> with your friends so they can use a SPXP client like the <a href="https://heyfolks.app" target="_blank">HeyFolks App</a> to follow your updates.<br/>(test: view <a href="%1$s" target="_blank">raw protocol data</a>)', 'hfa-spxp' ), get_option( 'siteurl' ) . '/spxp' ); ?>
        </p>
        <?php
    }

    function render_profile_name() {
        echo esc_html( get_option( 'blogname' ) )
        ?>
        <p class="description">
            <?php printf( __( 'Name of this profile. Identical to the <i>Site Title</i> as set in the <a href="%1$s">General Settings</a>.', 'hfa-spxp' ), admin_url( 'options-general.php' ) ); ?>
        </p>
        <?php
    }

    function render_profile_shortInfo() {
        echo esc_html( get_option( 'blogdescription' ) )
        ?>
        <p class="description">
            <?php printf( __( 'Short info about this profile. Identical to the <i>Tagline</i> as set in the <a href="%1$s">General Settings</a>.', 'hfa-spxp' ), admin_url( 'options-general.php' ) ); ?>
        </p>
        <?php
    }

    function get_image_html( $image_id ) {
        if( intval( $image_id ) > 0 ) {
            return wp_get_attachment_image( $image_id, 'thumbnail', false, array( 'id' => 'hfaspxp-profile-image' ) );
        } else {
            return '<span id="hfaspxp-profile-image">' . __('[&nbsp;No&nbsp;Profile&nbsp;Photo&nbsp;]', 'hfa-spxp') . '</span>';
        }
    }

    function render_profile_image() {
        // Kudos https://wordpress.stackexchange.com/a/236296
        $image_id = $this->options[ 'profile_image_id' ];
        echo $this->get_image_html( $image_id );
        ?>
        <input type="hidden" name="hfa-spxp[profile_image_id]" id="profile-image-id" value="<?php echo esc_attr( $image_id ); ?>" class="regular-text" />
        &nbsp;&nbsp;&nbsp;
        <input type='button' class="button-primary" value="<?php esc_attr_e( 'Select a Photo', 'hfa-spxp' ); ?>" id="hfaspxp-select-profile-image"/>
        &nbsp;
        <a id="hfaspxp-remove-profile-image" href="#"<?php
            if ( intval( $image_id ) <= 0 ) {
                echo ' style="display: none;"';
            }
        ?>><?php esc_attr_e( 'Remove Photo', 'hfa-spxp' ); ?></a>
        <?php
    }

    function render_profile_about() {
        ?>
        <textarea id="hfa_spxp_about" name="hfa-spxp[about]" rows="10" cols="80"><?php echo esc_textarea( $this->options[ 'about' ] ); ?></textarea>
        <p class="description">
            <?php _e('Say something about yourself (if you like).', 'hfa-spxp'); ?>
        </p>
        <?php
    }

    function render_post_type() {
        $spxp_post_types = array(
            'web-title' => __( 'Link to post using title only', 'hfa-spxp' ),
            'web-excerpt' => __( 'Link to post using excerpt (title if excerpt not available)', 'hfa-spxp' ),
            'txtimg-full' => __( 'Title and full text, image if available', 'hfa-spxp' ),
            'txtimg-excerpt' => __( 'Title and excerpt or text, image if available', 'hfa-spxp' ),
            'txtimg-excerpt-only' => __( 'Title and excerpt only, image if available', 'hfa-spxp' )
        );
        $spxp_post_type = $this->options['post_type'];
        ?>
        <select id="hfa_spxp_post_type" name="hfa-spxp[post_type]">
            <?php foreach ( $spxp_post_types as $key => $label ) { ?>
                <option value="<?php echo esc_html( $key ); ?>" <?php selected( $spxp_post_type, $key ); ?>><?php echo esc_html( $label ); ?></option>
            <?php } ?>
        </select>
        <p class="description">
            <?php _e('SPXP supports different types of posts (text, image, web). This setting controls which type is chosen and what information is included based on the post in wordpress.', 'hfa-spxp'); ?>
        </p>
        <?php
    }

    function render_post_preview_image_size() {
        $image_sizes = get_intermediate_image_sizes();
        $image_size = $this->options[ 'preview_image_size' ];
        ?>
        <select id="hfa_spxp_preview_image_size" name="hfa-spxp[preview_image_size]">
            <?php foreach ( $image_sizes as $image_size_name ) { ?>
                <option value="<?php echo esc_html( $image_size_name ); ?>" <?php selected( $image_size, $image_size_name ); ?>><?php echo esc_html( ucfirst( $image_size_name ) ); ?></option>
            <?php } ?>
        </select>
        <p class="description">
            <?php printf( __( 'Size of the preview image for picture posts. Customize image sizes in <a href="%1$s">media options</a>. Afterwards, don\'t forget to <a href="%2$s" target="_blank">regenerate thumbnails</a>.', 'hfa-spxp' ), admin_url( 'options-media.php' ), 'http://wordpress.org/plugins/regenerate-thumbnails/' ); ?>
        </p>
        <?php
    }

    function render_post_full_image_size() {
        $image_sizes = get_intermediate_image_sizes();
        array_unshift( $image_sizes, 'none' );
        $image_size = $this->options[ 'full_image_size' ];
        ?>
        <select id="hfa_spxp_full_image_size" name="hfa-spxp[full_image_size]">
            <?php foreach ( $image_sizes as $image_size_name ) { ?>
                <option value="<?php echo esc_html( $image_size_name ); ?>" <?php selected( $image_size, $image_size_name ); ?>><?php echo esc_html( ucfirst( $image_size_name ) ); ?></option>
            <?php } ?>
        </select>
        <p class="description">
            <?php printf( __( 'Size of the full image for picture posts. Customize image sizes in <a href="%1$s">media options</a>. Afterwards, don\'t forget to <a href="%2$s" target="_blank">regenerate thumbnails</a>.', 'hfa-spxp' ), admin_url( 'options-media.php' ), 'http://wordpress.org/plugins/regenerate-thumbnails/' ); ?>
        </p>
        <?php
    }

}