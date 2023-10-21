<?php

namespace Ternaryop\PhotoshelfUtil\Html;

class DownloadResult {
    private string $content;
    private string $mimeType;

    public function __construct(?string $content, ?string $mimeType) {
        $this->content = $content ?? '';
        $this->mimeType = $mimeType ?? '';
    }

    function getContent(): string {
        return $this->content;
    }

    function getMimeType(): string {
        return $this->mimeType;
    }
}
