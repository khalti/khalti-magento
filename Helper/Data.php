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
    // const IFTHENPAY_ENTIDADE = 'payment/fourwallsinn_khalti/fourwallsinn_entidade';
    // const IFTHENPAY_SUBENTIDADE = 'payment/fourwallsinn_khalti/fourwallsinn_subentidade';
    // const IFTHENPAY_ANTIPHISHING = 'payment/fourwallsinn_khalti/fourwallsinn_chave_anti_phishing';
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

/*    public function getEntidade()
    {
        return $this->scopeConfig->getValue(
            self::IFTHENPAY_ENTIDADE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }*/

    public function getMerchantId()
    {
        return $this->scopeConfig->getValue(
            self::ESEWA_MERCHANT_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
    }

/*    public function getSubentidade()
    {
        return $this->scopeConfig->getValue(
            self::IFTHENPAY_SUBENTIDADE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }*/

/*    public function getOrderState($orderid)
    {
        $order = $this->_orderFactory->create()->load($orderid);

        return $order->getStatus();
    }

    public function setOrderAsPaid($orderid)
    {
        $order = $this->_orderFactory->create()->load($orderid);

        $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)
            ->setStatus($order->getConfig()->getStateDefaultStatus(\Magento\Sales\Model\Order::STATE_PROCESSING));

        $order->save();
    }

    public function getOrderId($refid, $valor)
    {
        $select = $this->connection
            ->select()
            ->from($this->_orderTable, 'entity_id')
            ->where('entity_id LIKE \'%' . $refid . '\'')
            ->where('status = \'pending\'')
            ->where('grand_total = ' . $valor)
            ->order('created_at DESC');

        return $this->connection->fetchOne($select);
    }

    public function getAntiPhishing()
    {
        $chaveap = $this->scopeConfig->getValue(
            self::IFTHENPAY_ANTIPHISHING,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($chaveap == "" || $chaveap == null) {
            $chaveap=md5(time());

            $bindValues = ['path' => self::IFTHENPAY_ANTIPHISHING ];
            $select = $this->connection->select()->from($this->_configTable)->where('path = :path');
            $exists = $this->connection->fetchOne($select, $bindValues);

            $bind = ['value' => $chaveap];

            if ($exists) {
                $this->connection->update($this->_configTable, $bind, ['path=?' => self::IFTHENPAY_ANTIPHISHING]);
            } else {
                $bind['path'] = self::IFTHENPAY_ANTIPHISHING;
                $bind['value'] = $chaveap;
                $this->connection->insert($this->_configTable, $bind);
            }
        }

        return $chaveap;
    }

    public function checkIfAntiPhishingIsValid($ap)
    {
        return (
            $ap == $this->scopeConfig->getValue(
                self::IFTHENPAY_ANTIPHISHING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        );
    }*/
}
