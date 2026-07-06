<?php
declare(strict_types=1);

namespace BasicRum\Analytics\Block\Adminhtml\System\Config;

use BasicRum\Analytics\Model\Boomerang;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class BoomerangVersion extends Field
{
    protected function _getElementHtml(AbstractElement $element): string
    {
        return $this->escapeHtml(
            sprintf('Boomerang JS v.%s (continuity flavor) - %s', Boomerang::VERSION, Boomerang::SIZE_HINT)
        );
    }
}
