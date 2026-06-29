<?php

namespace HeyFolksApp\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Functions;
use HeyFolksApp_SPXP_Plugin;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class BuildPostItemTest extends TestCase {

    private HeyFolksApp_SPXP_Plugin $plugin;
    private ReflectionMethod $method;

    protected function setUp(): void {
        parent::setUp();
        Monkey\setUp();

        Functions\when( 'wp_strip_all_tags' )->alias( fn( $str ) => strip_tags( (string) $str ) );
        Functions\when( 'get_permalink' )->alias( fn( $post ) => 'https://example.com/' . $post->post_name . '/' );
        Functions\when( 'get_post_thumbnail_id' )->justReturn( 0 );
        Functions\when( 'wp_get_attachment_image_src' )->justReturn( false );
        Functions\when( 'get_attached_media' )->justReturn( [] );
        Functions\when( 'wp_get_attachment_url' )->justReturn( '' );

        $this->plugin = new HeyFolksApp_SPXP_Plugin();
        $this->method = new ReflectionMethod( HeyFolksApp_SPXP_Plugin::class, 'build_post_item' );
    }

    protected function tearDown(): void {
        Monkey\tearDown();
        parent::tearDown();
    }

    private function post( array $fields = [] ): \stdClass {
        $p                   = new \stdClass();
        $p->ID               = $fields['ID']               ?? 1;
        $p->post_name        = $fields['post_name']        ?? 'test-post';
        $p->post_title       = $fields['post_title']       ?? 'Test Title';
        $p->post_excerpt     = $fields['post_excerpt']     ?? '';
        $p->post_content     = $fields['post_content']     ?? 'Content';
        $p->post_date_gmt    = $fields['post_date_gmt']    ?? '2026-06-29 12:00:00';
        return $p;
    }

    private function item( \stdClass $post, string $post_type, string $preview = 'medium_large', string $full = 'none' ): array {
        return $this->method->invoke( $this->plugin, $post, $post_type, $preview, $full );
    }

    // --- seqts ---

    public function test_seqts_is_formatted_correctly(): void {
        $item = $this->item( $this->post( [ 'post_date_gmt' => '2026-06-29 15:30:45' ] ), 'txtimg-full' );
        $this->assertSame( '2026-06-29T15:30:45.000', $item['seqts'] );
    }

    // --- web type ---

    public function test_web_type_has_correct_fields(): void {
        $item = $this->item( $this->post(), 'web-title' );
        $this->assertSame( 'web', $item['type'] );
        $this->assertSame( 'https://example.com/test-post/', $item['link'] );
        $this->assertArrayHasKey( 'message', $item );
        $this->assertArrayNotHasKey( 'small', $item );
        $this->assertArrayNotHasKey( 'media', $item );
    }

    // --- text type (no thumbnail, no video) ---

    public function test_no_thumbnail_no_video_produces_text_type(): void {
        $item = $this->item( $this->post(), 'txtimg-full' );
        $this->assertSame( 'text', $item['type'] );
        $this->assertArrayNotHasKey( 'small', $item );
        $this->assertArrayNotHasKey( 'media', $item );
    }

    // --- photo type ---

    public function test_thumbnail_produces_photo_type(): void {
        Functions\when( 'get_post_thumbnail_id' )->justReturn( 10 );
        Functions\when( 'wp_get_attachment_image_src' )->justReturn( [ 'https://example.com/img.jpg', 800, 600, false ] );

        $item = $this->item( $this->post(), 'txtimg-full' );
        $this->assertSame( 'photo', $item['type'] );
        $this->assertSame( 'https://example.com/img.jpg', $item['small'] );
        $this->assertArrayNotHasKey( 'full', $item );
    }

    public function test_photo_type_includes_full_when_size_is_set(): void {
        Functions\when( 'get_post_thumbnail_id' )->justReturn( 10 );
        Functions\when( 'wp_get_attachment_image_src' )->justReturn( [ 'https://example.com/img.jpg', 800, 600, false ] );

        $item = $this->item( $this->post(), 'txtimg-full', 'medium_large', 'large' );
        $this->assertSame( 'photo', $item['type'] );
        $this->assertArrayHasKey( 'full', $item );
    }

    public function test_photo_type_omits_full_when_size_is_none(): void {
        Functions\when( 'get_post_thumbnail_id' )->justReturn( 10 );
        Functions\when( 'wp_get_attachment_image_src' )->justReturn( [ 'https://example.com/img.jpg', 800, 600, false ] );

        $item = $this->item( $this->post(), 'txtimg-full', 'medium_large', 'none' );
        $this->assertArrayNotHasKey( 'full', $item );
    }

    // --- video type ---

    public function test_video_and_thumbnail_produces_video_type(): void {
        $video        = new \stdClass();
        $video->ID    = 99;
        Functions\when( 'get_post_thumbnail_id' )->justReturn( 10 );
        Functions\when( 'wp_get_attachment_image_src' )->justReturn( [ 'https://example.com/thumb.jpg', 800, 600, false ] );
        Functions\when( 'get_attached_media' )->justReturn( [ $video ] );
        Functions\when( 'wp_get_attachment_url' )->justReturn( 'https://example.com/video.mp4' );

        $item = $this->item( $this->post(), 'txtimg-full' );
        $this->assertSame( 'video', $item['type'] );
        $this->assertSame( 'https://example.com/video.mp4', $item['media'] );
        $this->assertSame( 'https://example.com/thumb.jpg', $item['preview'] );
        $this->assertArrayNotHasKey( 'small', $item );
    }

    public function test_video_without_thumbnail_falls_back_to_text(): void {
        $video        = new \stdClass();
        $video->ID    = 99;
        Functions\when( 'get_attached_media' )->justReturn( [ $video ] );
        Functions\when( 'wp_get_attachment_url' )->justReturn( 'https://example.com/video.mp4' );

        $item = $this->item( $this->post(), 'txtimg-full' );
        $this->assertSame( 'text', $item['type'] );
    }

    // --- message is always present for txtimg types ---

    public function test_message_present_on_text_type(): void {
        $item = $this->item( $this->post( [ 'post_title' => 'Hello', 'post_content' => 'World' ] ), 'txtimg-full' );
        $this->assertSame( 'Hello World', $item['message'] );
    }
}
