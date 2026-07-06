<?php
declare(strict_types=1);

namespace BasicRum\Analytics\Model\Csp;

use BasicRum\Analytics\ViewModel\Footer;
use Magento\Csp\Api\PolicyCollectorInterface;
use Magento\Csp\Model\Policy\FetchPolicy;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Whitelists the configured BasicRUM beacon endpoint host under the fetch directives Boomerang uses
 * (XHR / navigator.sendBeacon => connect-src, image beacons => img-src) so beacons are not blocked
 * when Content-Security-Policy is enforced.
 *
 * The endpoint is admin-configurable, so it cannot be expressed as a static csp_whitelist.xml entry;
 * this collector reads the current value at request time instead.
 */
class BeaconPolicyCollector implements PolicyCollectorInterface
{
    /**
     * Fetch directives that must allow the beacon origin.
     */
    private const DIRECTIVES = ['connect-src', 'img-src'];

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * @inheritDoc
     */
    public function collect(array $defaultPolicies = []): array
    {
        $host = $this->getBeaconOrigin();
        if ($host === null) {
            return $defaultPolicies;
        }

        foreach (self::DIRECTIVES as $directive) {
            $defaultPolicies[] = new FetchPolicy($directive, false, [$host]);
        }

        return $defaultPolicies;
    }

    /**
     * Scheme + host (+ port) of the configured beacon endpoint, or null when unset/invalid.
     */
    private function getBeaconOrigin(): ?string
    {
        $endpoint = (string) $this->scopeConfig->getValue(
            Footer::XML_PATH_BEACON_ENDPOINT,
            ScopeInterface::SCOPE_STORE
        );

        if ($endpoint === '') {
            return null;
        }

        $parts = parse_url($endpoint);
        if ($parts === false || empty($parts['host'])) {
            return null;
        }

        $origin = isset($parts['scheme']) ? $parts['scheme'] . '://' . $parts['host'] : $parts['host'];
        if (isset($parts['port'])) {
            $origin .= ':' . $parts['port'];
        }

        return $origin;
    }
}
