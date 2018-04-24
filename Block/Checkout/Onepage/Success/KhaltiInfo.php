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
namespace Fourwallsinn\Khalti\Block\Checkout\Onepage\Success;

class KhaltiInfo extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    public $_checkoutSession;
    /**
     * @var \Magento\Customer\Model\Session
     */
    public $_customerSession;

    /**
     * @var \Magento\Paypal\Model\Billing\AgreementFactory
     */
    public $_agreementFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Paypal\Model\Billing\AgreementFactory $agreementFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Paypal\Model\Billing\AgreementFactory $agreementFactory,
        array $data = []
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_agreementFactory = $agreementFactory;


        parent::__construct($context, $data);
    }

    public function isFourwallsinnKhaltiPayment(){
        if ($this->getOrder()->getPayment()){
            if($this->getOrder()->getPayment()->getMethod() == 'khalti') {
                return true;
            }
        }
        return false;
    }

    public function getTotal()
    {
        return $this->getOrder()->formatPrice($this->getOrder()->getGrandTotal());
    }

    public function getOrder()
    {
        $order = $this->_checkoutSession->getLastRealOrder();

        return $order;
    }
}
