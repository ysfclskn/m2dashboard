<?php

namespace Tests\Unit;

use App\Support\Linkify;
use Illuminate\Support\HtmlString;
use Tests\TestCase;

class LinkifyTest extends TestCase
{
    // ── return type ───────────────────────────────────────────────────────────

    public function test_returns_html_string(): void
    {
        $this->assertInstanceOf(HtmlString::class, Linkify::text('hello'));
    }

    public function test_null_returns_empty(): void
    {
        $this->assertSame('', (string) Linkify::text(null));
    }

    // ── link conversion ───────────────────────────────────────────────────────

    public function test_https_url_becomes_anchor(): void
    {
        $out = (string) Linkify::text('Visit https://example.com now');
        $this->assertStringContainsString('<a href="https://example.com"', $out);
        $this->assertStringContainsString('target="_blank"', $out);
        $this->assertStringContainsString('rel="noopener noreferrer nofollow"', $out);
        $this->assertStringContainsString('https://example.com</a>', $out);
    }

    public function test_http_url_becomes_anchor(): void
    {
        $out = (string) Linkify::text('http://example.com');
        $this->assertStringContainsString('<a href="http://example.com"', $out);
    }

    public function test_markdown_style_link_becomes_anchor(): void
    {
        $out = (string) Linkify::text('[Example Site](https://example.com)');
        $this->assertStringContainsString('href="https://example.com"', $out);
        $this->assertStringContainsString('>Example Site</a>', $out);
    }

    public function test_url_with_query_string(): void
    {
        $out = (string) Linkify::text('https://example.com/path?a=1&b=2');
        $this->assertStringContainsString('href="https://example.com/path?a=1&amp;b=2"', $out);
    }

    public function test_trailing_punctuation_stripped_from_bare_url(): void
    {
        $out = (string) Linkify::text('See https://example.com.');
        $this->assertStringContainsString('href="https://example.com"', $out);
        // The trailing dot should appear as plain text after the closing anchor tag
        $this->assertStringContainsString('</a>.', $out);
    }

    public function test_surrounding_text_preserved(): void
    {
        $out = (string) Linkify::text('before https://example.com after');
        $this->assertStringContainsString('before ', $out);
        $this->assertStringContainsString(' after', $out);
    }

    public function test_multiple_urls_in_one_string(): void
    {
        $out = (string) Linkify::text('https://a.com and https://b.com');
        $this->assertStringContainsString('href="https://a.com"', $out);
        $this->assertStringContainsString('href="https://b.com"', $out);
    }

    // ── XSS escaping ─────────────────────────────────────────────────────────

    public function test_script_tag_is_escaped(): void
    {
        $out = (string) Linkify::text('<script>alert(1)</script>');
        $this->assertStringNotContainsString('<script>', $out);
        $this->assertStringContainsString('&lt;script&gt;', $out);
    }

    public function test_img_onerror_is_escaped(): void
    {
        $out = (string) Linkify::text('<img src=x onerror=alert(1)>');
        $this->assertStringNotContainsString('<img', $out);
        $this->assertStringContainsString('&lt;img', $out);
    }

    public function test_url_with_inline_attribute_injection_is_safe(): void
    {
        $out = (string) Linkify::text('https://example.com" onclick="alert(1)');
        // The injected attribute must not appear unescaped in the href
        $this->assertStringNotContainsString('" onclick="', $out);
    }

    public function test_normal_text_remains_visible(): void
    {
        $out = (string) Linkify::text('Hello world');
        $this->assertStringContainsString('Hello world', $out);
    }

    // ── unsafe protocols ─────────────────────────────────────────────────────

    public function test_javascript_protocol_not_linked(): void
    {
        $out = (string) Linkify::text('javascript:alert(1)');
        $this->assertStringNotContainsString('<a ', $out);
        $this->assertStringContainsString('javascript:alert(1)', $out);
    }

    public function test_markdown_link_with_javascript_protocol_not_linked(): void
    {
        $out = (string) Linkify::text('[click](javascript:alert(1))');
        $this->assertStringNotContainsString('<a ', $out);
    }

    public function test_data_uri_not_linked(): void
    {
        $out = (string) Linkify::text('data:text/html,<h1>test</h1>');
        $this->assertStringNotContainsString('<a ', $out);
    }
}
