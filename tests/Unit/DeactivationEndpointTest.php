<?php

namespace HeyFolksApp\Tests\Unit;

use Brain\Monkey;
use HeyFolksApp_SPXP_Plugin;
use PHPUnit\Framework\TestCase;

class DeactivationEndpointTest extends TestCase {

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

    public function test_spxp_endpoint_is_removed(): void {
        $endpoint = [ EP_ROOT, 'spxp', 'spxp' ];
        $this->assertFalse( $this->plugin->deactivation_is_spxp_endpoint( $endpoint ) );
    }

    public function test_non_spxp_endpoint_is_kept(): void {
        $endpoint = [ EP_ROOT, 'feed', 'feed' ];
        $this->assertTrue( $this->plugin->deactivation_is_spxp_endpoint( $endpoint ) );
    }

    public function test_spxp_with_different_mask_is_kept(): void {
        // Only EP_ROOT + 'spxp' + 'spxp' is the plugin's own endpoint
        $endpoint = [ 1, 'spxp', 'spxp' ];
        $this->assertTrue( $this->plugin->deactivation_is_spxp_endpoint( $endpoint ) );
    }
}
