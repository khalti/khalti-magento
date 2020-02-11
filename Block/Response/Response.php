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

use Fourwallsinn\Khalti\Helper\Data;
use Magento\Checkout\Model\Order;
use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Sales\Model\OrderFactory;

/**
 * Abstract class for Cash On Delivery and Bank Transfer payment method form
 */
class Response extends Template
{
    /**
     * @var Session
     */
    protected $_checkoutSession;

    /**
     * @var Order
     */
    protected $_order;

        /**
     * @var Order
     */
    protected $_orderFactory;

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * Response constructor.
     * @param Context $context
     * @param OrderFactory $orderFactory
     * @param Session $checkoutSession
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        OrderFactory $orderFactory,
        Session $checkoutSession,
        Data $helper
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
            /** @var AbstractMethod $method */
            $method = $this->getMethod();
            $this->_instructions = $method->getConfigData('instructions');
        }
        return $this->_instructions;
    }

    /**
    * Get the Order Id
    *
    * @return int increment id
    */
    public function getOrderId()
    {
        return $this->_checkoutSession->getLastRealOrderId();
    }

    /**
    * Get the Grand Total.
    *
    * @return float grand total id
    */
    public function getTotal()
    {
        $tot =  $this->_checkoutSession->getLastRealOrder()->getGrandTotal();
        return $tot*100;
    }

    /**
    * Get the Order Id
    *
    * @return int increment id
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
     * @return Session
     */
    private function _getCheckout()
    {
        return $this->_checkoutSession;
    }
}
