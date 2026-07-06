<?php
declare(strict_types=1);

namespace BasicRum\Analytics\Model;

/**
 * Single source of truth for the bundled Boomerang RUM library.
 *
 * When upgrading the vendored build, update {@see self::VERSION} and replace the file the
 * {@see self::JS_VIEW_PATH} view path resolves to — the path itself stays version-agnostic so the
 * template never needs editing.
 */
class Boomerang
{
    /**
     * Bundled Boomerang build. Format: <major>.<buildCount>.<flavorRevision>.
     * "60" is the "cutting-edge" plugin flavor (bundles the Continuity plugin) of release build 815.
     */
    public const VERSION = '1.815.60';

    /**
     * Approximate transferred size, shown in the admin for information only.
     */
    public const SIZE_HINT = '~30 KB (gzipped)';

    /**
     * Version-agnostic view path of the bundled, minified library.
     */
    public const JS_VIEW_PATH = 'BasicRum_Analytics::js/boomr/boomerang.min.js';
}
