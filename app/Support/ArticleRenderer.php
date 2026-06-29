<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Turns the stored article HTML into the final rendered HTML:
 *   1. converts [IMAGE: prompt | alt] markers into responsive <figure> images
 *      (keyless AI image generation via the configured endpoint),
 *   2. inserts display-ad units after the intro and between sections.
 */
class ArticleRenderer
{
    public static function render(string $html): string
    {
        $html = self::injectImages($html);
        $html = self::injectAds($html);

        return $html;
    }

    public static function imageUrl(string $prompt, int $width = 1200, int $height = 630): string
    {
        $cfg = config('blog.images');
        $endpoint = rtrim($cfg['endpoint'], '/').'/';
        $seed = abs(crc32($prompt)); // stable image per prompt

        return $endpoint.rawurlencode(Str::limit($prompt, 380, ''))
            ."?width={$width}&height={$height}&seed={$seed}&".($cfg['extra'] ?? 'nologo=true');
    }

    private static function injectImages(string $html): string
    {
        if (! config('blog.images.enabled')) {
            return preg_replace('/\[IMAGE:.*?]/s', '', $html);
        }

        return preg_replace_callback('/\[IMAGE:\s*(.*?)\s*]/s', function ($m) {
            [$prompt, $alt] = array_pad(array_map('trim', explode('|', $m[1], 2)), 2, '');
            $alt = $alt !== '' ? $alt : Str::limit($prompt, 120, '');
            $url = self::imageUrl($prompt, 1200, 600);

            return '<figure class="article-figure">'
                .'<img src="'.e($url).'" alt="'.e($alt).'" loading="lazy" width="1200" height="600">'
                .'<figcaption>'.e($alt).'</figcaption></figure>';
        }, $html);
    }

    private static function injectAds(string $html): string
    {
        // Show ONE in-article ad, placed in the middle of the post (before the
        // middle-most H2 section). The right-hand sidebar carries the other ad.
        $headings = preg_match_all('/<h2\b/i', $html);
        if ($headings < 2) {
            return $html; // too short to place a sensible in-article ad
        }

        $adUnit = view('partials.ad', ['slotKey' => 'in_article', 'label' => 'in-article'])->render();
        $target = (int) ceil($headings / 2); // the middle section heading

        $seen = 0;

        return preg_replace_callback('/<h2\b/i', function ($m) use (&$seen, $target, $adUnit) {
            $seen++;

            return $seen === $target ? $adUnit.$m[0] : $m[0];
        }, $html, 1 + $target); // limit is enough to reach the target heading
    }
}
