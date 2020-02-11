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

use \Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const KHALTI_LIVE_PUBLIC_KEY = 'payment/khalti/khalti_live_public_key';
    const KHALTI_LIVE_SECRET_KEY = 'payment/khalti/khalti_live_secret_key';
    const KHALTI_TEST_PUBLIC_KEY = 'payment/khalti/khalti_test_public_key';
    const KHALTI_TEST_SECRET_KEY = 'payment/khalti/khalti_test_secret_key';
    const KHALTI_MODE            = 'payment/khalti/khalti_test_mode';

    public      $connection;
    protected   $_checkoutSession;
    public      $_orderFactory;


    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getMerchantId()
    {
        return $this->scopeConfig->getValue(
            self::ESEWA_MERCHANT_ID,
            ScopeInterface::SCOPE_STORE
            );
    }

    /**
     * Get Khalti Mode, 1 for Live, 0 for Test
     */
    public function getKhaltiMode()
    {
        return $this->scopeConfig->getValue(self::KHALTI_MODE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Khalti Public Key
     */
    public function getPublicKey()
    {
        if(!$this->getKhaltiMode()){
            return $this->scopeConfig->getValue(
                self::KHALTI_LIVE_PUBLIC_KEY,
                ScopeInterface::SCOPE_STORE
                );
        } else {
            return $this->scopeConfig->getValue(
                self::KHALTI_TEST_PUBLIC_KEY,
                ScopeInterface::SCOPE_STORE
                );
        }

    }

    /**
     * Get Khalti Private Key
     */
    public function getSecretKey()
    {
        if(!$this->getKhaltiMode()){
            return $this->scopeConfig->getValue(
                self::KHALTI_LIVE_SECRET_KEY,
                ScopeInterface::SCOPE_STORE
                );
        } else {
            return $this->scopeConfig->getValue(
                self::KHALTI_TEST_SECRET_KEY,
                ScopeInterface::SCOPE_STORE
                );
        }
    }
}
