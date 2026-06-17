<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

class Linkify
{
    private const ANCHOR_CLASS = 'text-amber-400 hover:text-amber-300 underline underline-offset-2';

    /**
     * Convert plain URLs and markdown-style links in user text to safe anchor tags.
     * All non-link text is HTML-escaped. Only http/https protocols are allowed.
     */
    public static function text(?string $value): HtmlString
    {
        if ($value === null || $value === '') {
            return new HtmlString('');
        }

        // Single-pass: find markdown [label](url) and bare https?:// URLs together,
        // escape everything between matches, build anchors only for validated URLs.
        $pattern = '/\[([^\]]{1,300})\]\((https?:\/\/[^)\s]{1,2000})\)|(https?:\/\/\S{1,2000})/u';

        $result = '';
        $offset = 0;

        preg_match_all($pattern, $value, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        foreach ($matches as $match) {
            $matchStart = $match[0][1];
            $matchLen   = strlen($match[0][0]);

            // Escape and append literal text before this match
            $result .= htmlspecialchars(
                substr($value, $offset, $matchStart - $offset),
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );

            if (!empty($match[3][0])) {
                // Bare URL — strip trailing punctuation that is unlikely to be part of the URL
                $raw  = rtrim($match[3][0], '.,;:!?)\'"><');
                $tail = substr($match[3][0], strlen($raw));

                $result .= self::anchor($raw, htmlspecialchars($raw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
                $result .= htmlspecialchars($tail, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            } else {
                // Markdown [label](url)
                $result .= self::anchor(
                    $match[2][0],
                    htmlspecialchars($match[1][0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
                );
            }

            $offset = $matchStart + $matchLen;
        }

        // Escape any remaining literal text after the last match
        $result .= htmlspecialchars(substr($value, $offset), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return new HtmlString($result);
    }

    private static function anchor(string $rawUrl, string $escapedLabel): string
    {
        if (!preg_match('/^https?:\/\//i', $rawUrl)) {
            return $escapedLabel;
        }

        $href = htmlspecialchars($rawUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return '<a href="' . $href . '" target="_blank" rel="noopener noreferrer nofollow" class="' . self::ANCHOR_CLASS . '">'
            . $escapedLabel . '</a>';
    }
}
