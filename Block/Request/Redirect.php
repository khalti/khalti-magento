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
namespace Fourwallsinn\Khalti\Block\Request;


use Fourwallsinn\Khalti\Helper\Data;
use Magento\Checkout\Model\Order;
use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Sales\Model\OrderFactory;

class Redirect extends Template
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

    public $_template = 'request/redirect.phtml';

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
    * @return order increment id
    */
    public function getOrderId()
    {
        print_r($this->_checkoutSession->getLastRealOrderId()) ;
        exit;
    }

    /**
    * Get the Grand Total
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

    public function getPublicKey()
    {
        return $this->_helper->getPublicKey();
    }

    public function getSecretKey()
    {
        return $this->_helper->getSecretKey();
    }

    public function getKhaltiMode()
    {
        return $this->_helper->getKhaltiMode();
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
