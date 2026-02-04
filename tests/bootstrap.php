<?php

declare(strict_types=1);

/**
 * PHPUnit Bootstrap File
 *
 * Defines WordPress function stubs for testing.
 */

// Load Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Define WordPress translation function stubs
if (!function_exists('__')) {
    /**
     * Stub for WordPress __ translation function.
     *
     * @param string $text   Text to translate.
     * @param string $domain Text domain.
     * @return string Original text (no translation in tests).
     */
    function __(string $text, string $domain = 'default'): string
    {
        return $text;
    }
}

if (!function_exists('esc_html__')) {
    /**
     * Stub for WordPress esc_html__ function.
     *
     * @param string $text   Text to translate.
     * @param string $domain Text domain.
     * @return string Original text.
     */
    function esc_html__(string $text, string $domain = 'default'): string
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_html')) {
    /**
     * Stub for WordPress esc_html function.
     *
     * @param string $text Text to escape.
     * @return string Escaped text.
     */
    function esc_html(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_url')) {
    /**
     * Stub for WordPress esc_url function.
     *
     * @param string $url URL to escape.
     * @return string Escaped URL.
     */
    function esc_url(string $url): string
    {
        return filter_var($url, FILTER_SANITIZE_URL) ?: '';
    }
}

if (!function_exists('wp_kses_post')) {
    /**
     * Stub for WordPress wp_kses_post function.
     *
     * @param string $text Text to sanitize.
     * @return string Sanitized text.
     */
    function wp_kses_post(string $text): string
    {
        return $text; // In tests, just return as-is
    }
}
