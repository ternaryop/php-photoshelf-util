<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Ternaryop\PhotoshelfUtil\Html\HtmlUtil;

class UrlTest extends TestCase {

  public function testSrcSet(): void {
    $expected = [
      480 => 'elva-fairy-480w.jpg',
      800 => 'elva-fairy-800w.jpg'
    ];
    $result = HtmlUtil::parseSrcSet('elva-fairy-480w.jpg 480w, elva-fairy-800w.jpg 800w');

    $this->assertEquals($expected, $result);
  }

  public function testCanonicalUrl(): void {
    $html = <<<EOD
<html lang="en">
<head>
<title>Sample Doc</title>
<link rel="canonical" href="https://example.com/dresses/green-dresses" />
</head>
</html>
EOD;

    $document = HtmlUtil::htmlDocument($html);
    $url = HtmlUtil::canonicalUrl($document);

    $this->assertEquals('https://example.com/dresses/green-dresses', $url);
  }

  public function testDoNotContainCanonicalUrl(): void {
    $html = <<<EOD
<html lang="en">
<head>
<title>Sample Doc</title>
</head>
</html>
EOD;

    $document = HtmlUtil::htmlDocument($html);
    $url = HtmlUtil::canonicalUrl($document);

    $this->assertTrue($url === null);
  }

  public function testParseUrl(): void {
    $expected = [
      'scheme' => 'https',
      'host' => 'www.example.com',
      'path' => '/c%d0%b0te-bl%d0%b0nchett-3rd-check-style-in-los-angeles-2017-10-24.html',
      'port' => 7654,
    ];
    $result = HtmlUtil::parse_url('https://www.example.com:7654/c%d0%b0te-bl%d0%b0nchett-3rd-check-style-in-los-angeles-2017-10-24.html');

    $this->assertEquals($expected, $result);
  }

  public function testAbsUrlAbsolute(): void {
    $baseuri = 'https://www.example.com';
    $rel = 'https://www.example.com/name-surname-heading-to-rosso-in-town-1970-10-11.html/name-surname-heading-to-rosso-in-town-01';
    $expected = 'https://www.example.com/name-surname-heading-to-rosso-in-town-1970-10-11.html/name-surname-heading-to-rosso-in-town-01';
    $result = HtmlUtil::absUrl($baseuri, $rel);

    $this->assertEquals($expected, $result);
  }

  public function testAbsUrlRelative(): void {
    $baseuri = 'https://www.example.com';
    $rel = 'name-surname-heading-to-rosso-in-town-1970-10-11.html/name-surname-heading-to-rosso-in-town-01';
    $expected = 'https://www.example.com/name-surname-heading-to-rosso-in-town-1970-10-11.html/name-surname-heading-to-rosso-in-town-01';
    $result = HtmlUtil::absUrl($baseuri, $rel);

    $this->assertEquals($expected, $result);
  }

  public function testEncodeUrlRfc3986(): void {
    $url = 'www.example.com/name-surname-heading';
    $expected = 'http://www.example.com/name-surname-heading';
    $result = HtmlUtil::encode_url_rfc_3986($url);

    $this->assertEquals($expected, $result);
  }

  public function testUnParseUrl(): void {
    $source = [
      'user' => 'jenna',
      'pass' => '123456',
      'fragment' => 'bookmark'
    ];
    $expected = 'jenna:123456@#bookmark';
    $result = HtmlUtil::unparse_url($source);

    $this->assertEquals($expected, $result);
  }
}
