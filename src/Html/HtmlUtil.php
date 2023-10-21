<?php
namespace Ternaryop\PhotoshelfUtil\Html;

use QueryPath\DOMQuery;
use Ternaryop\PhotoshelfUtil\String\StringUtil;

class HtmlUtil {
  const USER_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:52.0) Gecko/20100101 Firefox/52.0';
  const OPTION_POST_DATA = 'post_data';
  const OPTION_USER_AGENT = 'user_agent';
  const OPTION_COOKIE = 'cookie';

  public static function downloadHtml(string $url, array $options = null): string {
    $result = HtmlUtil::downloadUrl($url, $options);
    if (strpos($result->getMimeType(), "text/html") === 0) {
      return $result->getContent();
    }
    throw new DownloadException("Only HTML urls are allowed, found " . $result->getMimeType());
  }

  /**
   * @param string $url the url to download
   * @param array|null $options could contain (post_data, user_agent, cookie)
   * Return DownloadResult the result
   * @return DownloadResult
   */
  public static function downloadUrl(string $url, array $options = null): DownloadResult {
    $ch = curl_init();

    $post_data = null;
    $user_agent = null;
    $cookie = null;

    if ($options !== null) {
      $post_data = $options[self::OPTION_POST_DATA] ?? null;
      $user_agent = $options[self::OPTION_USER_AGENT] ?? HtmlUtil::USER_AGENT;
      $cookie = $options[self::OPTION_COOKIE] ?? null;
    }

    // encoding the query string generates a lot of problems (e.g. invalid urls)
    // so we encode only the necessary characters
    $url = str_replace(' ', '%20', $url);

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    if ($cookie !== null) {
      curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    }

    if ($post_data !== null) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
      curl_setopt($ch, CURLOPT_POST, 1);
    }

    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);

    $result = new DownloadResult(
      curl_exec($ch),
      curl_getinfo($ch, CURLINFO_CONTENT_TYPE)
    );

    $error = null;
    if (curl_errno($ch)) {
      $error = curl_error($ch);
    }
    curl_close($ch);

    if ($error) {
      throw new DownloadException($error);
    }

    return $result;
  }

  /**
   * Return an htmlqp document
   */
  public static function htmlDocument(string $content): DOMQuery {
    // Ensure UTF-8 is correctly handled
    $qp_options = array(
      'convert_from_encoding' => mb_detect_encoding($content, 'UTF-8, ISO-8859-1'),
      'convert_to_encoding' => 'UTF-8',
      'strip_low_ascii' => FALSE,
    );

    // Without this the encoding is lost by DOMDocument::loadHTML
    $content = HtmlUtil::prependEncoding($content);
    return htmlqp($content, NULL, $qp_options);
  }

  public static function prependEncoding(string $str): string {
    $XML_ENC = '<?xml encoding="utf-8" ?>';
    if (StringUtil::startsWith($str, $XML_ENC)) {
      return $str;
    }
    return $XML_ENC . "\n" . $str;
  }

  public static function encode_url_rfc_3986(string $url): string {
    $chunks = HtmlUtil::parse_url($url);
    if (!isset($chunks['path'])) {
      return $url;
    }
    $path = HtmlUtil::encode_url_path($chunks['path']);
    $port = isset($chunks['port']) ? ':' . $chunks['port'] : '';
    $query = isset($chunks['query']) ? '?' . $chunks['query'] : '';
    return $chunks['scheme'] . '://' . $chunks['host'] . $port . $path . $query;
  }

  public static function encode_url_path(string $path): string {
    $path_components = explode('/', $path);
    $encode_path = '';

    foreach ($path_components as $idx => $p) {
      if ($idx > 0) {
        $encode_path .= '/';
      }
      $encode_path .= rawurlencode($p);
    }
    return $encode_path;
  }

  public static function parseUrlOrThrow(string $url): array {
    if (empty($url)) {
      throw new ParseUrlException("Url is empty");
    }
    if (strpos($url, 'http') !== 0) {
      throw new ParseUrlException("Url found is not http");
    }
    $comp = parse_url($url);
    if ($comp === false) {
      throw new ParseUrlException("Url is malformed");
    }
    return $comp;
  }

  // UTF-8 aware parse_url() replacement
  // http://bluebones.net/2013/04/parse_url-is-not-utf-8-safe/
  // see http://php.net/manual/en/function.parse-url.php#114817
  public static function parse_url(string $url): array {
    $result = false;

    // Build arrays of values we need to decode before parsing
    $entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%24', '%2C', '%2F', '%3F', '%23', '%5B', '%5D');
    $replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "$", ",", "/", "?", "#", "[", "]");

    // Create encoded URL with special URL characters decoded, so it can be parsed
    // All other characters will be encoded
    $encodedURL = str_replace($entities, $replacements, urlencode($url));

    // Parse the encoded URL
    $encodedParts = parse_url($encodedURL);

    // Now, decode each value of the resulting array
    if ($encodedParts) {
      foreach ($encodedParts as $key => $value) {
        $result[$key] = urldecode(str_replace($replacements, $entities, (string)$value));
      }
    }
    return $result;
  }

  public static function unparse_url(array $parsed_url): string {
    $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
    $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
    $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
    $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
    $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
    $pass     = ($user || $pass) ? "$pass@" : '';
    $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
    $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
    $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

    return "$scheme$user$pass$host$port$path$query$fragment";
  }

  /**
   * Find, if present, the canonical link.
   * This handle correctly urls compliant with Google AMP
   */
  public static function canonicalUrl(DOMQuery $htmlDocument): ?string {
    $link = $htmlDocument->find('link[rel="canonical"]');
    if ($link->size() == 0) {
      return null;
    }
    $url = $link->attr('href');
    if (empty(trim($url))) {
      return null;
    }
    return $url;
  }

  public static function absUrl(string $baseuri, string $rel): string {
    if (strpos($rel, "http://") === 0 || strpos($rel, "https://") === 0) {
      return $rel;
    }
    return $baseuri . "/" . $rel;
  }

  /**
   * Return the map (width, url) for HTML srcset attribute's content sorted by width
   */
  public static function parseSrcSet(string $srcSet): ?array {
    if ($srcSet == null || trim($srcSet) == '') {
      return null;
    }
    $map = array();
    foreach (explode(",", $srcSet) as $src) {
      // split url and width
      $pair = explode(' ', trim($src));
      if (count($pair) == 2) {
        $int_width = intval(str_replace('w', '', trim($pair[1])));
        $map[$int_width] = trim($pair[0]);
      }
    }
    ksort($map);
    return $map;
  }

  public static function resolveUrl(string $originalUrl, DOMQuery $htmlDocument): string {
    $url = HtmlUtil::canonicalUrl($htmlDocument);
    if ($url == null || $url == $originalUrl) {
      return $originalUrl;
    }
    $comp = HtmlUtil::parse_url($originalUrl);
    $baseuri = $comp['scheme'] . "://" . $comp['host'];
    return HtmlUtil::absUrl($baseuri, $url);
  }
}

