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

namespace Fourwallsinn\Khalti\Model;

class Khalti extends \Magento\Payment\Model\Method\AbstractMethod
{
    const PAYMENT_METHOD_ESEWA_CODE = 'khalti';

  /**
   * Payment method code
   *
   * @var string
   */
    public $_code = self::PAYMENT_METHOD_ESEWA_CODE;
    public $_formBlockType = 'Fourwallsinn\Khalti\Block\Form\Khalti';
    public $_infoBlockType = 'Fourwallsinn\Khalti\Block\Info\Khalti';
    //public $_isOffline = true;
    protected $_isInitializeNeeded      = true;
    protected $_canUseInternal          = true;
    protected $_canUseForMultishipping  = false;


    //  public function getInstructions()
    // {
    //     return trim($this->getConfigData('instructions'));
    // }


}
