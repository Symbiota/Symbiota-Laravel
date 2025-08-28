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
        return base_path(getenv('PORTAL_NAME') . $path);
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
        return url(
            (getenv('PORTAL_USE_CLIENT_ROOT') === 'true' ?
                getenv('PORTAL_NAME') : '') . $path
        );
    }
}
