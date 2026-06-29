<?php

namespace HeyFolksApp\Tests\Unit;

use Brain\Monkey;
use HeyFolksApp_SPXP_Plugin;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase {

    private HeyFolksApp_SPXP_Plugin $plugin;

    protected function setUp(): void {
        parent::setUp();
        Monkey\setUp();
        $this->plugin = new HeyFolksApp_SPXP_Plugin();
    }

    protected function tearDown(): void {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function test_spxp_key_absent_leaves_vars_unchanged(): void {
        $vars = [ 'page' => 'home' ];
        $this->assertSame( $vars, $this->plugin->request( $vars ) );
    }

    public function test_spxp_key_empty_string_becomes_true(): void {
        $result = $this->plugin->request( [ 'spxp' => '' ] );
        $this->assertTrue( $result['spxp'] );
    }

    public function test_spxp_key_with_value_is_unchanged(): void {
        $result = $this->plugin->request( [ 'spxp' => 'posts' ] );
        $this->assertSame( 'posts', $result['spxp'] );
    }

    public function test_other_keys_are_preserved(): void {
        $result = $this->plugin->request( [ 'spxp' => '', 'page' => 'home' ] );
        $this->assertTrue( $result['spxp'] );
        $this->assertSame( 'home', $result['page'] );
    }
}
