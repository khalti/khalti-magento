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

namespace Fourwallsinn\Khalti\Block\Info;

use Magento\Framework\Registry;

class Khalti extends \Magento\Payment\Block\Info
{
    public $_quote;
    public $coreRegistry = null;
    public $_checkoutSession = null;
    public $_order = null;
    public $__data=null;

    /**
     * @var string
     */
    public $_template = 'info/khalti.phtml';

    public $_logger;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Registry $registry,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order $order,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->_checkoutSession = $checkoutSession;
        $this->_order = $order;
        $this->__data = $data;

        $this->_logger = $context->getLogger();

        parent::__construct($context, $data);
    }

}
