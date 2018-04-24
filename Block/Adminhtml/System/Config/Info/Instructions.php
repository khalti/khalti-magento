<?php
/**
* Fourwallsinn_Khalti
*
* @category    Payment Gateway
* @package     Fourwallsinn_Khalti
* @author      4 Walls Innovations
* @copyright   4 Walls Innovations (http://www.4wallsinn.com)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

/**
 * Renderer for Payments Advanced information
 */
namespace Fourwallsinn\Khalti\Block\Adminhtml\System\Config\Info;

use Magento\Framework\ObjectManagerInterface;

class Instructions extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Template path
     *
     * @var string
     */
    public $_template = 'system/config/info/instructions.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Render fieldset html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $columns = $this->getRequest()->getParam('website') || $this->getRequest()->getParam('store') ? 5 : 4;
        return $this->_decorateRowHtml($element, "<td colspan='{$columns}'>" . $this->toHtml() . '</td>');
    }

/*    public function getEntidade()
    {
        return $this->_ifthenpayMbHelper->getEntidade();
    }

    public function getSubentidade()
    {
        return $this->_ifthenpayMbHelper->getSubentidade();
    }

    public function getUrlCallback()
    {
        return $this->_storeManager->getStore()->getBaseUrl().
        "ifthenpay/Callback/Check/k/[CHAVE_ANTI_PHISHING]/e/[ENTIDADE]/r/[REFERENCIA]/v/[VALOR]";
    }

    public function getAntiPhishingKey()
    {
        return $this->_ifthenpayMbHelper->getAntiPhishing();
    }*/
}
