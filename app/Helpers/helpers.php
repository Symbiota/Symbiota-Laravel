<?php

/**
 * Helper to easily import submoduled portals for tooling that has
 * not converted to laravel.
 *
 * @param String  $path Path starting at sub moduled portal
 * @return String
 **/
if(!function_exists('legacy_path')) {
    function legacy_path(string $path): string {
        if(mb_substr($path, 0, 1) !== '/') {
            $path = '/' . $path;
        }

        return base_path(config('portal.name') . $path);
    }
}
