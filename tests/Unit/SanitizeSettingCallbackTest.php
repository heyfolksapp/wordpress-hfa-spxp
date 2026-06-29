<?php

namespace HeyFolksApp\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Functions;
use HeyFolksApp_SPXP_Plugin;
use PHPUnit\Framework\TestCase;

class SanitizeSettingCallbackTest extends TestCase {

    private HeyFolksApp_SPXP_Plugin $plugin;

    protected function setUp(): void {
        parent::setUp();
        Monkey\setUp();

        Functions\when( 'sanitize_textarea_field' )->returnArg();
        Functions\when( 'sanitize_key' )->alias( fn( $v ) => strtolower( preg_replace( '/[^a-z0-9_\-]/i', '', (string) $v ) ) );
        Functions\when( 'absint' )->alias( fn( $v ) => abs( (int) $v ) );
        Functions\when( 'load_plugin_textdomain' )->justReturn();
        Functions\when( 'register_setting' )->justReturn();
        Functions\when( 'add_rewrite_endpoint' )->justReturn();
        Functions\when( 'get_option' )->justReturn( false );

        $this->plugin = new HeyFolksApp_SPXP_Plugin();
    }

    protected function tearDown(): void {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function test_valid_post_types_are_accepted(): void {
        $valid = [ 'web-title', 'web-excerpt', 'txtimg-full', 'txtimg-excerpt', 'txtimg-excerpt-only' ];
        foreach ( $valid as $type ) {
            $result = $this->plugin->sanitize_setting_callback( [ 'post_type' => $type ] );
            $this->assertSame( $type, $result['post_type'], "Expected '$type' to be accepted" );
        }
    }

    public function test_invalid_post_type_falls_back_to_default(): void {
        $result = $this->plugin->sanitize_setting_callback( [ 'post_type' => 'made-up-type' ] );
        $this->assertSame( 'txtimg-full', $result['post_type'] );
    }

    public function test_missing_post_type_falls_back_to_default(): void {
        $result = $this->plugin->sanitize_setting_callback( [] );
        $this->assertSame( 'txtimg-full', $result['post_type'] );
    }

    public function test_missing_keys_produce_safe_defaults(): void {
        $result = $this->plugin->sanitize_setting_callback( [] );
        $this->assertSame( 0, $result['profile_image_id'] );
        $this->assertSame( '', $result['about'] );
        $this->assertSame( 'medium_large', $result['preview_image_size'] );
        $this->assertSame( 'none', $result['full_image_size'] );
    }

    public function test_profile_image_id_is_cast_to_absint(): void {
        $result = $this->plugin->sanitize_setting_callback( [ 'profile_image_id' => '-42' ] );
        $this->assertSame( 42, $result['profile_image_id'] );
    }

    public function test_return_value_always_contains_all_keys(): void {
        $result = $this->plugin->sanitize_setting_callback( [] );
        foreach ( [ 'about', 'profile_image_id', 'post_type', 'preview_image_size', 'full_image_size' ] as $key ) {
            $this->assertArrayHasKey( $key, $result );
        }
    }
}
