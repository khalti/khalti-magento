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
namespace Fourwallsinn\Khalti\Block\Process;

use Symfony\Component\Config\Definition\Exception\Exception;

class Process extends \Magento\Payment\Block\Form
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $_checkoutSession;

    /**
     * @var \Magento\Checkout\Model\Order
     */
    private $_order;

    /**
     * @var \Fourwallsinn\Khalti\Helper\Data
     */
    private $_helper;

    /**
     * Process constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\OrderFactory                $orderFactory
     * @param \Magento\Checkout\Model\Session                  $checkoutSession
     * @param \Fourwallsinn\Khalti\Helper\Data                 $helper
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Fourwallsinn\Khalti\Helper\Data $helper,
        array $data = []
    ) {
        $this->_orderFactory = $orderFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_helper = $helper;
        parent::__construct($context, $data);
        $this->_getOrder();
    }

    /**
     * @return string
     */
    public function getFormUrl()
    {
        $result = '';
        try {
            $order = $this->_order;
            if ($order->getPayment()) {
                $result = $this->_order->getPayment()->getMethodInstance()->getFormUrl();
            }
        } catch (Exception $e) {
            // do nothing for now
            throw($e);
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getFormFields()
    {
        $result = [];
        try {
            if ($this->_order->getPayment()) {
                $result = $this->_order->getPayment()->getMethodInstance()->getFormFields();
            }
        } catch (Exception $e) {
            // do nothing for now
            $this->_helper->logDebug('Form fields exception:'.$e);
        }

        return $result;
    }

    /**
     * Get order object.
     *
     * @return \Magento\Sales\Model\Order
     */
    private function _getOrder()
    {
        if (!$this->_order) {
            $incrementId = $this->_getCheckout()->getLastRealOrderId();
            $this->_order = $this->_orderFactory->create()->loadByIncrementId($incrementId);
        }

        return $this->_order;
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
