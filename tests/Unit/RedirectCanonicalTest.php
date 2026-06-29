<?php

namespace HeyFolksApp\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Functions;
use HeyFolksApp_SPXP_Plugin;
use PHPUnit\Framework\TestCase;

class RedirectCanonicalTest extends TestCase {

    private HeyFolksApp_SPXP_Plugin $plugin;

    protected function setUp(): void {
        parent::setUp();
        Monkey\setUp();

        Functions\when( 'home_url' )->alias( fn( string $path = '' ) => 'https://example.com' . $path );

        $this->plugin = new HeyFolksApp_SPXP_Plugin();
    }

    protected function tearDown(): void {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function test_spxp_root_redirect_is_suppressed(): void {
        $this->assertFalse( $this->plugin->redirect_canonical( 'https://example.com/spxp/' ) );
    }

    public function test_spxp_posts_redirect_is_suppressed(): void {
        $this->assertFalse( $this->plugin->redirect_canonical( 'https://example.com/spxp/posts/' ) );
    }

    public function test_other_redirects_pass_through(): void {
        $url = 'https://example.com/my-post/';
        $this->assertSame( $url, $this->plugin->redirect_canonical( $url ) );
    }

    public function test_spxp_subpath_is_not_suppressed(): void {
        // /spxp/something-else/ should not be blocked
        $url = 'https://example.com/spxp/feed/';
        $this->assertSame( $url, $this->plugin->redirect_canonical( $url ) );
    }
}
