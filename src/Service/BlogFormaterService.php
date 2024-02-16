<?php

namespace App\Service;

use RtfHtmlPhp\Document;
use RtfHtmlPhp\Html\HtmlFormatter;

class BlogFormaterService
{
    public function getFormate($blob): string
    {
        $document = '';
        $formatter = new HtmlFormatter();
        try {
            $document = new Document($blob);
        } catch (\Throwable $th) {
        }
        if (!$document) {
            $texte = '';
        } else {
            $texte = $formatter->Format($document);
        }
        return $texte;
    }
}
