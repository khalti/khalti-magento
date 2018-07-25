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

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Fourwallsinn\Khalti\Helper\Data as KhaltiHelper;
class KhaltiConfigProvider implements ConfigProviderInterface
{
    protected $khaltiHelper;

    const CODE = 'khalti';

    /**
     * @var \Magento\Payment\Model\Method\AbstractMethod[]
     */
    public $methods = [];

    /**
     * @var Escaper
     */
    public $escaper;

    /**
     * @param PaymentHelper $paymentHelper
     * @param Escaper $escaper
     */
    public function __construct(
        KhaltiHelper $paymentHelper,
        Escaper $escaper
    ) {
        $this->escaper = $escaper;
        $this->khaltiHelper = $paymentHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {

        return [
            'payment' => [
                self::CODE => [
                    "khalti_public_key" => $this->khaltiHelper->getPublicKey()
                ]
            ]
        ]; 
    }

    /**
     * Get instructions text from config
     *
     * @param string $code
     * @return string
     */
    public function getInstructions($code)
    {
        return nl2br($this->escaper->escapeHtml($this->methods[$code]->getInstructions()));
    }
}
