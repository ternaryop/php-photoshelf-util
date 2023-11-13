<?php

declare(strict_types=1);

namespace Ternaryop\PhotoshelfUtil\Html;

class DownloadResult {
  private string $content;
  private string $mimeType;

  public function __construct(bool|string $content, bool|string $mimeType) {
    $this->content = $content === false ? '' : strval($content);
    $this->mimeType = $mimeType === false ? '' : strval($mimeType);
  }

  function getContent(): string {
    return $this->content;
  }

  function getMimeType(): string {
    return $this->mimeType;
  }
}
