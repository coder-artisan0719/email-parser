<?php

namespace App\Services;

use ZBateson\MailMimeParser\MailMimeParser;
use App\Traits\HtmlTextConverterTrait;

class EmailParserService
{
    use HtmlTextConverterTrait;
    /**
     * Parse raw email content and extract plain text
     */
    public function parseEmail(string $rawEmail): string
    {
        $parser = new MailMimeParser();

        $message = $parser->parse($rawEmail, false);

        // Get plain text content
        $plainText = $message->getTextContent();
        
        // If no plain text content is found, try to extract from HTML
        if (empty($plainText) && $message->getHtmlContent()) {
            $plainText = $this->convertHtmlToText($message->getHtmlContent());
        }

        // Clean the text
        return $this->cleanText($plainText ?? '');
    }
}