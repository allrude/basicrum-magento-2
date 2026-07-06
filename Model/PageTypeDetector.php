<?php
declare(strict_types=1);

namespace BasicRum\Analytics\Model;

use BasicRum\Analytics\Api\PageTypeDetectorInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\Http as HttpResponse;

class PageTypeDetector implements PageTypeDetectorInterface
{
    /**
     * Full action name => page type.
     */
    private const PAGE_TYPE_MAP = [
        'cms_index_index' => 'home',
        'cms_page_view' => 'cms_page',
        'cms_noroute_index' => '404_not_found',
        'catalog_product_view' => 'product',
        'catalog_category_view' => 'category',
        'checkout_index_index' => 'checkout',
        'checkout_cart_index' => 'cart',
        'customer_account_login' => 'customer_login',
        'customer_account_create' => 'customer_register',
        'customer_account_index' => 'customer_account',
        'sales_order_history' => 'order_history',
        'contact_index_index' => 'contact',
        'catalogsearch_result_index' => 'search_results',
    ];

    private ?string $pageType = null;

    public function __construct(
        private readonly HttpRequest $request,
        private readonly HttpResponse $response
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getPageType(): string
    {
        if ($this->pageType !== null) {
            return $this->pageType;
        }

        // An explicit 404 status wins over the action-name mapping.
        if ($this->response->getStatusCode() === 404) {
            return $this->pageType = '404_not_found';
        }

        $fullActionName = (string) $this->request->getFullActionName();

        return $this->pageType = self::PAGE_TYPE_MAP[$fullActionName] ?? 'unmapped_' . $fullActionName;
    }
}
