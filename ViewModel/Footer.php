<?php
declare(strict_types=1);

namespace BasicRum\Analytics\ViewModel;

use BasicRum\Analytics\Api\PageTypeDetectorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class Footer implements ArgumentInterface
{
    public const XML_PATH_BEACON_ENDPOINT = 'basicrum/general/beacon_endpoint';
    public const XML_PATH_TOKEN = 'basicrum/general/token';

    /**
     * Query-string parameter the token is appended as.
     */
    private const TOKEN_PARAM = 'token';

    public function __construct(
        private readonly PageTypeDetectorInterface $pageTypeDetector,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly EncryptorInterface $encryptor
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
     * Decrypted beacon token / site key for the current store (empty string when unset).
     */
    public function getToken(): string
    {
        $stored = (string) $this->scopeConfig->getValue(
            self::XML_PATH_TOKEN,
            ScopeInterface::SCOPE_STORE
        );

        return $stored === '' ? '' : (string) $this->encryptor->decrypt($stored);
    }

    /**
     * Full beacon URL: the configured endpoint with the token appended as a query parameter when set.
     * Returns an empty string when no endpoint is configured.
     */
    public function getBeaconUrl(): string
    {
        $endpoint = $this->getBeaconEndpoint();
        if ($endpoint === '') {
            return '';
        }

        $token = $this->getToken();
        if ($token === '') {
            return $endpoint;
        }

        $separator = str_contains($endpoint, '?') ? '&' : '?';

        return $endpoint . $separator . self::TOKEN_PARAM . '=' . rawurlencode($token);
    }

    /**
     * Coarse-grained page type for the current request.
     */
    public function getPageType(): string
    {
        return $this->pageTypeDetector->getPageType();
    }
}
