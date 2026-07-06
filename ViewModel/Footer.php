<?php
declare(strict_types=1);

namespace BasicRum\Analytics\ViewModel;

use BasicRum\Analytics\Api\PageTypeDetectorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class Footer implements ArgumentInterface
{
    public const XML_PATH_BEACON_ENDPOINT = 'basicrum/general/beacon_endpoint';

    public function __construct(
        private readonly PageTypeDetectorInterface $pageTypeDetector,
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * Configured beacon collector endpoint for the current store (empty string when unset).
     */
    public function getBeaconEndpoint(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_BEACON_ENDPOINT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Coarse-grained page type for the current request.
     */
    public function getPageType(): string
    {
        return $this->pageTypeDetector->getPageType();
    }
}
