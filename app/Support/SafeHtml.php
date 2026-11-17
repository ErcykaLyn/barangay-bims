<?php

namespace App\Support;

class SafeHtml
{
    private const ALLOWED = ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li', 'a', 'h2', 'h3', 'h4', 'blockquote'];
    private const DROP    = ['script', 'style', 'iframe', 'object', 'embed', 'form', 'svg'];

    public static function render(?string $content): string
    {
        $content = trim((string) $content);

        if ($content === '') {
            return '';
        }

        // Plain text: paragraphs + auto-link
        if ($content === strip_tags($content)) {
            return collect(preg_split('/\R{2,}/u', $content))
                ->map(function ($para) {
                    $safe = nl2br(e(trim($para)));
                    $safe = preg_replace(
                        '~(https?://[^\s<]+)~i',
                        '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
                        $safe
                    );

                    return '<p>' . $safe . '</p>';
                })
                ->implode("\n");
        }

        // HTML: linisin
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(
            '<?xml encoding="utf-8"?><div>' . $content . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        $root = $dom->getElementsByTagName('div')->item(0);
        if (!$root) {
            return e(strip_tags($content));
        }

        self::clean($root);

        $html = '';
        foreach ($root->childNodes as $node) {
            $html .= $dom->saveHTML($node);
        }

        return $html;
    }

    private static function clean(\DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if (!$child instanceof \DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);

            if (in_array($tag, self::DROP, true)) {
                $node->removeChild($child);
                continue;
            }

            self::clean($child);

            // Hindi pinapayagang tag: alisin ang tag, itira ang laman
            if (!in_array($tag, self::ALLOWED, true)) {
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }

            $href = $tag === 'a' ? trim($child->getAttribute('href')) : '';

            foreach (iterator_to_array($child->attributes) as $attr) {
                $child->removeAttribute($attr->nodeName);
            }

            if ($tag === 'a' && preg_match('#^(https?://|mailto:|tel:)#i', $href)) {
                $child->setAttribute('href', $href);
                $child->setAttribute('target', '_blank');
                $child->setAttribute('rel', 'noopener noreferrer');
            }
        }
    }
}