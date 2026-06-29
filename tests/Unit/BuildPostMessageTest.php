<?php

namespace HeyFolksApp\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Functions;
use HeyFolksApp_SPXP_Plugin;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class BuildPostMessageTest extends TestCase {

    private HeyFolksApp_SPXP_Plugin $plugin;
    private ReflectionMethod $method;

    protected function setUp(): void {
        parent::setUp();
        Monkey\setUp();

        Functions\when( 'wp_strip_all_tags' )->alias( fn( $str ) => strip_tags( (string) $str ) );

        $this->plugin = new HeyFolksApp_SPXP_Plugin();
        $this->method = new ReflectionMethod( HeyFolksApp_SPXP_Plugin::class, 'build_post_message' );
    }

    protected function tearDown(): void {
        Monkey\tearDown();
        parent::tearDown();
    }

    private function post( string $title = '', string $excerpt = '', string $content = '' ): \stdClass {
        $p                = new \stdClass();
        $p->post_title    = $title;
        $p->post_excerpt  = $excerpt;
        $p->post_content  = $content;
        return $p;
    }

    private function msg( \stdClass $post, string $post_type ): string {
        return $this->method->invoke( $this->plugin, $post, $post_type );
    }

    // --- web-title ---

    public function test_web_title_returns_title(): void {
        $this->assertSame( 'My Post', $this->msg( $this->post( 'My Post', 'excerpt' ), 'web-title' ) );
    }

    public function test_web_title_ignores_excerpt(): void {
        $this->assertSame( 'Title', $this->msg( $this->post( 'Title', 'some excerpt' ), 'web-title' ) );
    }

    // --- web-excerpt ---

    public function test_web_excerpt_returns_excerpt_when_present(): void {
        $this->assertSame( 'The excerpt', $this->msg( $this->post( 'Title', 'The excerpt' ), 'web-excerpt' ) );
    }

    public function test_web_excerpt_falls_back_to_title_when_excerpt_empty(): void {
        $this->assertSame( 'Title', $this->msg( $this->post( 'Title', '' ), 'web-excerpt' ) );
    }

    // --- txtimg-full ---

    public function test_txtimg_full_combines_title_and_content(): void {
        $this->assertSame( 'Title Full body', $this->msg( $this->post( 'Title', '', 'Full body' ), 'txtimg-full' ) );
    }

    // --- txtimg-excerpt ---

    public function test_txtimg_excerpt_uses_excerpt_when_present(): void {
        $this->assertSame( 'T Short', $this->msg( $this->post( 'T', 'Short', 'Long' ), 'txtimg-excerpt' ) );
    }

    public function test_txtimg_excerpt_falls_back_to_content_when_excerpt_empty(): void {
        $this->assertSame( 'T Long', $this->msg( $this->post( 'T', '', 'Long' ), 'txtimg-excerpt' ) );
    }

    // --- txtimg-excerpt-only ---

    public function test_txtimg_excerpt_only_uses_excerpt(): void {
        $this->assertSame( 'T Short', $this->msg( $this->post( 'T', 'Short', 'Long' ), 'txtimg-excerpt-only' ) );
    }

    public function test_txtimg_excerpt_only_ignores_content(): void {
        $this->assertSame( 'T ', $this->msg( $this->post( 'T', '', 'Long' ), 'txtimg-excerpt-only' ) );
    }

    // --- HTML stripping ---

    public function test_html_tags_are_stripped_from_title(): void {
        $this->assertSame( 'Bold Title content', $this->msg( $this->post( '<b>Bold Title</b>', '', 'content' ), 'txtimg-full' ) );
    }

    public function test_html_tags_are_stripped_from_content(): void {
        $this->assertSame( 'T Para', $this->msg( $this->post( 'T', '', '<p>Para</p>' ), 'txtimg-full' ) );
    }
}
