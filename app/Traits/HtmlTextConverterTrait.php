<?php

namespace App\Traits;

trait HtmlTextConverterTrait
{
    /**
     * Convert HTML to plain text
     */
    public function convertHtmlToText($html): string
    {
        // Remove head section including title
        $html = preg_replace('/<head\b[^>]*>(.*?)\/head>/is', '', $html);

        // Remove scripts, styles, and HTML comments
        $html = preg_replace([
            '/<script\b[^>]*>(.*?)<\/script>/is',
            '/<style\b[^>]*>(.*?)<\/style>/is',
            '/<!--(.*?)-->/s'
        ], '', $html);
        
        // Replace common HTML elements with text equivalents
        $replacements = [
            '/<br\s*\/?>/i' => "\n",
            '/<\/p>/i' => "\n\n",
            '/<\/h[1-6]>/i' => "\n\n",
            '/<li>/i' => "• ",
            '/<\/li>/i' => "\n"
        ];
        
        $html = preg_replace(array_keys($replacements), array_values($replacements), $html);
        
        // Remove all remaining HTML tags
        $text = strip_tags($html);
        
        // Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Normalize whitespace but preserve line breaks
        $text = preg_replace([
            '/[ \t]+/',      // Replace multiple spaces/tabs with a single space
            '/^ +| +$/m',    // Remove leading/trailing spaces on each line
            '/\n{3,}/'       // Replace 3+ consecutive line breaks with just 2
        ], [
            ' ',
            '',
            "\n\n"
        ], $text);
        
        return trim($text);
    }
    
    /**
     * Clean text to only include printable characters and line breaks
     */
    public function cleanText($text): string
    {
        // Keep only printable characters and line breaks
        $text = preg_replace('/[^\P{C}\n]+/u', '', $text);
        
        // Normalize line breaks
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        
        return trim($text);
    }
}