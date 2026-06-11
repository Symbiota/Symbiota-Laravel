<?php

/**
 * Helper to easily import submoduled portals for tooling that has
 * not converted to laravel.
 *
 * @param  string  $path  Path starting at sub moduled portal
 * @return string
 **/
if (! function_exists('legacy_path')) {
    function legacy_path(string $path): string {
        return base_path(config('portal.name') . '/' . ltrim($path, '/'));
    }
}

/**
 * Helper to control links to submoduled portals. Some use client roots which
 * subfolders access.
 *
 * @param  string  $path  Path starting at sub moduled portal
 * @return string
 **/
if (! function_exists('legacy_url')) {
    function legacy_url(string $path = ''): string {
        if (config('portal.use_client_root')) {
            $path = config('portal.name') . '/' . ltrim($path, '/');
        }

        return url($path);
    }
}

/**
 * Helper to generate URLs to Symbiota documentation.
 * Makes documentation links resilient to URL changes.
 *
 * @param  string  $path  Path to append to the docs base URL
 * @return string
 **/
if (! function_exists('docs_url')) {
    function docs_url(string $path = ''): string {
        $baseUrl = 'https://docs.symbiota.org/';

        return $baseUrl . ltrim($path, '/');
    }
}

if (! function_exists('item')) {
    function item(mixed $value, ?string $title = null, bool $disabled = false): array {
        return ['value' => $value, 'title' => $title ?? $value, 'disabled' => $disabled];
    }
}

if (! function_exists('itemize')) {
    function itemize(array $values, array $defaults = []): array {
        $items = $defaults;
        foreach ($values as $key => $label) {
            $items[] = item($key, $label);
        }

        return $items;
    }
}

if (! function_exists('itemize_assoc')) {
    function itemize_assoc(array $values, callable $map_fn, array $defaults = []): array {
        $items = $defaults;
        foreach ($values as $key => $assoc) {
            $items[] = item($key, $map_fn($assoc));
        }

        return $items;
    }
}

if (! function_exists('itemize_flat')) {
    function itemize_flat(array $values, array $defaults = []): array {
        $items = $defaults;
        foreach ($values as $value) {
            $items[] = item($value);
        }

        return $items;
    }
}

if (! function_exists('message_bag')) {
    function message_bag(array $messages): \Illuminate\Support\MessageBag {
        return new \Illuminate\Support\MessageBag($messages);
    }
}
