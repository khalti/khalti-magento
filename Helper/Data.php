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

namespace Fourwallsinn\Khalti\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ESEWA_MERCHANT_ID = 'payment/khalti/khalti_merchant_id';

    public $_configTable;
    public $_orderTable;
    public $connection;
    protected $_checkoutSession;
    public $_orderFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->_configTable = $resource->getTableName('core_config_data');
        $this->_orderTable = $resource->getTableName('sales_order');
        $this->_orderFactory = $orderFactory;
        $this->connection = $resource->getConnection();

        parent::__construct($context);
        $this->_checkoutSession = $checkoutSession;
    }

    public function getMerchantId()
    {
        return $this->scopeConfig->getValue(
            self::ESEWA_MERCHANT_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
    }
}
