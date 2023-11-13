<?php

declare(strict_types=1);

namespace Ternaryop\PhotoshelfUtil\Array;

use stdClass;

class ArrayMapUtil {
  /**
   * An empty map is returned as empty array (i.e. []),
   * this creates problem on client JSON parsers because they expect a map but receive an array,
   * so we replace the empty map with stdClass to return an empty object (e.g. {})
   * @param array<string, mixed> $map
   * @return array<string, mixed>|stdClass
   */
  public static function safeEmptyMap(array $map): array|stdClass {
    return empty($map) ? new stdClass() : $map;
  }

  static function fromStdClass(mixed $data): mixed
  {
    if (is_object($data)) {
      $data = get_object_vars($data);
    }

    if (is_array($data)) {
      return array_map(__METHOD__, $data);
    } else {
      return $data;
    }
  }

}

