<?php

declare(strict_types=1);

namespace Ternaryop\PhotoshelfUtil\Array;

class RangeUtil {
  /**
   * Return the value checking it is in range
   * if value < min then return min
   * if value < max then return max
   * Otherwise return value
   */
  public static function boundValue(int $value, int $min, int $max): mixed {
    return min(max($min, $value), $max);
  }
}
