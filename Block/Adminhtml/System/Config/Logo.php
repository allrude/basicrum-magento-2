<?php
declare(strict_types=1);

namespace BasicRum\Analytics\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Logo extends Field
{
    /**
     * @var string
     */
    protected $_template = 'BasicRum_Analytics::system/config/logo.phtml';

    /**
     * Render the field as a full-width banner (no label/scope columns).
     */
    public function render(AbstractElement $element): string
    {
        return $this->_toHtml();
    }

    protected function _getElementHtml(AbstractElement $element): string
    {
        return $this->_toHtml();
    }

    /**
     * Static URL of the BasicRUM logo asset.
     */
    public function getLogoUrl(): string
    {
        return $this->getViewFileUrl('BasicRum_Analytics::images/basicrum-log.svg');
    }
}
