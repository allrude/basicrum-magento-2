<?php
declare(strict_types=1);

namespace BasicRum\Analytics\Api;

/**
 * Resolves a coarse-grained page type for the current storefront request.
 *
 * @api
 */
interface PageTypeDetectorInterface
{
    /**
     * Get the current page type (e.g. "home", "product", "checkout", "404_not_found").
     *
     * @return string
     */
    public function getPageType(): string;
}
