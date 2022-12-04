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

class HeyFolksApp_SPXP_Plugin {

    const OPTION_GROUP = 'hfa-spxp-settings';
    const OPTION_NAME = 'hfa-spxp';
    const OPTION_ACTIVATION_FLAG = 'hfa-spxp-activation-flag';

    public function activation() {
        if ( ! get_option( self::OPTION_ACTIVATION_FLAG ) ) {
            add_option( self::OPTION_ACTIVATION_FLAG, true );
        }
    }

    public function deactivation() {
        global $wp_rewrite;
        $wp_rewrite->endpoints = array_filter( $wp_rewrite->endpoints, [ $this, 'deactivation_is_spxp_endpoint' ] );
        flush_rewrite_rules( false );
    }

    public function deactivation_is_spxp_endpoint( $endpoint ) {
        return ! ( $endpoint[0] === EP_ROOT && $endpoint[1] === 'spxp' && $endpoint[2] === 'spxp' );
    }

    public function add_hooks() {
        add_action( 'init', [ $this, 'init' ] );
        add_filter( 'request', [ $this, 'request' ] );
        add_filter( 'redirect_canonical', [ $this, 'redirect_canonical' ] );
        add_action( 'template_redirect', [ $this, 'template_redirect' ] );
    }

    public function init() {
        load_plugin_textdomain( 'hfa-spxp' );
        register_setting(
            self::OPTION_GROUP,
            self::OPTION_NAME,
            [
                'sanitize_callback' => [ $this, 'sanitize_setting_callback' ],
                'default'           => [
                    'post_type'          => 'txtimg-full',
                    'preview_image_size' => 'medium_large',
                    'full_image_size'    => 'none'
                ]
            ]
        );
        add_rewrite_endpoint( 'spxp', EP_ROOT );
        if ( get_option( self::OPTION_ACTIVATION_FLAG ) ) {
            flush_rewrite_rules( false );
            delete_option( self::OPTION_ACTIVATION_FLAG );
        }
    }

    public function sanitize_setting_callback( $value ) {
        return $value;
    }

    public function request( $vars ) {
        if ( isset( $vars['spxp'] ) && empty( $vars['spxp'] ) ) {
            $vars['spxp'] = true;
        }
        return $vars;
    }

    public function redirect_canonical( $redirect_url ) {
        if ( $redirect_url === get_option( 'siteurl' ) . '/spxp/' ) {
            return false;
        }
        if ( $redirect_url === get_option( 'siteurl' ) . '/spxp/posts/' ) {
            return false;
        }
        return $redirect_url;
    }

    public function template_redirect() {
        if ( get_query_var( 'spxp' ) ) {
            $this->handle_spxp( get_query_var( 'spxp' ) );
            exit();
        }
    }

    function handle_spxp( $spxp_slug ) {
        if ( $spxp_slug === true ) {
            $this->handle_spxp_root();
        }
        else if( $spxp_slug === 'posts' ) {
            $this->handle_spxp_posts();
        }
    }

    function handle_spxp_root() {
        $options = get_option( self::OPTION_NAME );
        $response = array(
            'ver'           => '0.3',
            'name'          => get_option( 'blogname' ),
            'shortInfo'     => get_option( 'blogdescription' ),
            'website'       => home_url(),
            'postsEndpoint' => get_option( 'siteurl' ) . '/spxp/posts'
        );
        $about = trim( $options[ 'about' ] );
        if ( strlen( $about ) > 0 ) {
            $response[ 'about' ] = $about;
        }
        $image_id = $options[ 'profile_image_id' ];
        if( intval( $image_id ) > 0 ) {
            $image = wp_get_attachment_image_src( $image_id, 'medium_large', false );
            if ( $image ) {
                $response[ 'profilePhoto' ] = $image[0];
            }
        }
        header( 'Content-Type: application/json' );
        echo json_encode( $response );
    }

    function handle_spxp_posts()
    {
        $options = get_option( self::OPTION_NAME );
        $post_type = $options[ 'post_type' ];
        $preview_image_size = $options[ 'preview_image_size' ];
        $full_image_size = $options[ 'full_image_size' ];
        $utc_timezone = new DateTimeZone( 'UTC' );
        $before = DateTime::createFromFormat( 'Y-m-d\TH:i:s.u', $_GET[ 'before' ], $utc_timezone );
        $after = DateTime::createFromFormat( 'Y-m-d\TH:i:s.u', $_GET[ 'after' ], $utc_timezone );
        $max = 50;
        if ( isset( $_GET[ 'max' ] ) && is_numeric( $_GET[ 'max' ] ) ) {
            $max = intval( $_GET[ 'max' ] );
            if ( $max < 1 ) {
                $max = 1;
            } elseif ( $max > 100 ) {
                $max = 100;
            }
        }
        $args = array(
            'numberposts' => $max + 1
        );
        if ( $before || $after ) {
            $args[ 'date_query' ] = array();
            $args[ 'date_query' ][ 'column' ] = 'post_date_gmt';
            if ( $before ) {
                $args[ 'date_query' ][ 'before' ] = $before->setTimezone( $utc_timezone )->format( 'Y-m-d H:i:s' );
            }
            if ( $after ) {
                $args[ 'date_query' ][ 'after' ] = $after->setTimezone( $utc_timezone )->format( 'Y-m-d H:i:s' );
            }
        }
        $latest_posts = get_posts( $args );
        $response_data = array();
        foreach ( $latest_posts as $post ) {
            if ( ! empty( $post->post_password ) ) {
                continue;
            }
            $spxp_post = array(
                'seqts'   => substr( $post->post_date_gmt, 0, 10) . 'T' . substr( $post->post_date_gmt, 11, 8).'.000'
            );
            if ( substr( $post_type, 0, 4) == 'web-' ) {
                $spxp_post[ 'type' ] = 'web';
                $spxp_post[ 'link' ] = get_permalink( $post );
                $title = trim( wp_strip_all_tags( $post->post_title ) );
                $excerpt = trim( wp_strip_all_tags( $post->post_excerpt ) );
                $spxp_post[ 'message' ] = ( $post_type == 'web-title' ) || ( strlen( $excerpt ) == 0 ) ? $title : $excerpt;
            } elseif ( substr( $post_type, 0, 7) == 'txtimg-' ) {
                $thumbnail_id = get_post_thumbnail_id( $post->ID );
                $small_image_src = null;
                $full_image_src = null;
                if ( $thumbnail_id > 0 && $preview_image_size ) {
                    if ( in_the_loop() ) {
                        update_post_thumbnail_cache();
                    }
                    $image = wp_get_attachment_image_src( $thumbnail_id, $preview_image_size, false );
                    if ( $image ) {
                        $small_image_src = $image[0];
                    }
                    if( $full_image_size && $full_image_size !== 'none' ) {
                        $image = wp_get_attachment_image_src( $thumbnail_id, $full_image_size, false );
                        if ( $image ) {
                            $full_image_src = $image[0];
                        }
                    }
                }
                if ( $small_image_src ) {
                    $spxp_post[ 'type' ] = 'photo';
                    $spxp_post[ 'small' ] = $small_image_src;
                    if ( $full_image_src ) {
                        $spxp_post[ 'full' ] = $full_image_src;
                    }
                } else {
                    $spxp_post[ 'type' ] = 'text';
                }
                $title = trim( wp_strip_all_tags( $post->post_title ) );
                $excerpt = trim( wp_strip_all_tags( $post->post_excerpt ) );
                $content = trim( wp_strip_all_tags( $post->post_content ) );
                switch ( $post_type ) {
                    case 'txtimg-full':
                        // Title and full text, image if available
                        $spxp_post[ 'message' ] = $title . ' ' . $content;
                        break;
                    case 'txtimg-excerpt':
                        // Title and excerpt or text, image if available
                        $spxp_post[ 'message' ] = $title . ' ' . ( strlen( $excerpt ) > 0 ? $excerpt : $content );
                        break;
                    case 'txtimg-excerpt-only':
                        // Title and excerpt only, image if available
                        $spxp_post[ 'message' ] = $title . ' ' . $excerpt;
                        break;
                }
            }
            array_push( $response_data, $spxp_post );
            if ( count( $response_data ) >= $max ) {
                break;
            }
        }
        $response = array(
            'data' => $response_data,
            'more' => count( $latest_posts ) > $max
        );
        header('Content-Type: application/json');
        echo json_encode($response);
    }

}