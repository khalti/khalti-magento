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
namespace Fourwallsinn\Khalti\Block\Response;

/**
 * Abstract class for Cash On Delivery and Bank Transfer payment method form
 */
class Response extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Checkout\Model\Order
     */
    protected $_order;

        /**
     * @var \Magento\Checkout\Model\Order
     */
    protected $_orderFactory;

    /**
     * @var \RealexPayments\HPP\Helper\Data
     */
    protected $_helper;

    //public $_template = 'response/redirect.phtml';

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Fourwallsinn\Khalti\Helper\Data $helper
    )
    {
        
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_helper = $helper;
        parent::__construct($context);
    }
    /**
     * Instructions text
     *
     * @var string
     */
    public $_instructions;

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * Get instructions text from config
     *
     * @return null|string
     */
    public function getInstructions()
    {
        if ($this->_instructions === null) {
            /** @var \Magento\Payment\Model\Method\AbstractMethod $method */
            $method = $this->getMethod();
            $this->_instructions = $method->getConfigData('instructions');
        }
        return $this->_instructions;
    }

    /**
    * Get the Order Id
    *
    * @return order increment id
    */
    public function getOrderId()
    {
        return $this->_checkoutSession->getLastRealOrderId();
    }

    /**
    * Get the Grand Total. Multiplying by 100 because our base currency is in Dollar
    *
    * @return order grand total id
    */
    public function getTotal()
    {
        $tot =  $this->_checkoutSession->getLastRealOrder()->getGrandTotal();
        return $tot*100;
    }

    /**
    * Get the Order Id
    *
    * @return order increment id
    */
    public function getMerchantId()
    {
        return $this->_helper->getMerchantId();
    }


        /**
     * Get order object.
     *
     * @return \Magento\Sales\Model\Order
     */
    private function getOrder()
    {
        return $this->_checkoutSession->getLastRealOrder();
    }

    /**
     * Get frontend checkout session object.
     *
     * @return \Magento\Checkout\Model\Session
     */
    private function _getCheckout()
    {
        return $this->_checkoutSession;
    }
}
