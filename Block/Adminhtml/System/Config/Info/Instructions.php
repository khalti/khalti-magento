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

/**
 * Renderer for Payments Advanced information
 */
namespace Fourwallsinn\Khalti\Block\Adminhtml\System\Config\Info;

use Magento\Framework\ObjectManagerInterface;
use Fourwallsinn\Khalti\Helper\Data as KhaltiHelper;

class Instructions extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Template path
     *
     * @var string
     */
    public $_template = 'system/config/info/instructions.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        KhaltiHelper $paymentHelper,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->khaltiHelper = $paymentHelper;
        $this->request = $request;
        parent::__construct($context);
    }

    /**
     * Render fieldset html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $columns = $this->getRequest()->getParam('website') || $this->getRequest()->getParam('store') ? 5 : 4;
        return $this->_decorateRowHtml($element, "<td colspan='{$columns}'>" . $this->toHtml() . '</td>');
    }

    /**
     * Get Transaction List from API
     */
    public function getTransactions()
    {
        $transaction = [];
        $getTransaction = $this->getTransaction()['Response'];
        $getTransaction = json_decode($getTransaction);
        if(isset($getTransaction->records)){
            foreach($getTransaction->records as $t)
            {
                array_push($transaction, array(
                    'idx' 		=> $t->idx,
                    'source' 	=> $t->user->name,
                    'amount' 	=> $t->amount/100,
                    'fee' 		=> $t->fee_amount/100,
                    'date' 		=> date("Y/m/d H:m:s", strtotime($t->created_on)),
                    'type' 		=> $t->type->name,
                    'state' 	=> $t->refunded == true ? "Refunded" : $t->state->name,
                    'refunded' 	=> $t->refunded,
                ));
            }
        }

        return $transaction;
    }

    /**
     * Get Transaction Detail from API
     */
    public function transactionDetail()
    {
        $transaction_id = @$this->request->getParams()["transaction_id"];
        $refund = @$this->request->getParams()["refund"];
        if($refund == 'true'){
            $refund = $this->khaltiRefund($transaction_id);
            $status_code = $refund['StatusCode'];
            $detail = json_decode($refund['Response']);
            $detail = $detail->detail;
            if($status_code == 200){
              $alertMessage = sprintf("Success: %s",$detail);
            } else {
              $alertMessage = sprintf("Error: %s",$detail);
            }
            echo "<script>alert('".$alertMessage."');</script>";
        }

        if($transaction_id){
            $transaction_detail = json_decode($this->getTransactionDetail($transaction_id)['Response']);
                //
                $transaction_detail_array = array(
                    "idx" => $transaction_detail->idx,
                    "source" => $transaction_detail->user->name,
                    "mobile" => $transaction_detail->user->mobile,
                    "amount" => $transaction_detail->amount/100,
                    "fee_amount" => $transaction_detail->fee_amount/100,
                    "date" => date("Y/m/d H:m:s", strtotime($transaction_detail->created_on)),
                    "state" => $transaction_detail->refunded == true ? "Refunded" : $transaction_detail->state->name,
                    "refunded" => $transaction_detail->refunded,
                );
            return $transaction_detail_array;
        }
    }

    private function getTransaction()
    {
        $url = "https://khalti.com/api/merchant-transaction/";

        # Make the call using API.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $headers = ['Authorization: Key '.$this->khaltiHelper->getSecretKey()];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Response
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return array(
            "Response" => $response,
            "StatusCode" => $status_code
        );
    }

    public function getTransactionDetail($idx)
    {
        $url = "https://khalti.com/api/merchant-transaction/{$idx}/";

        # Make the call using API.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $headers = ['Authorization: Key '.$this->khaltiHelper->getSecretKey()];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Response
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
            return array(
                "Response" => $response,
                "StatusCode" => $status_code
            );
    }

    public function khaltiRefund($idx)
    {
        $url = "https://khalti.com/api/merchant-transaction/{$idx}/refund/";
        # Make the call using API.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = ['Authorization: Key '.$this->khaltiHelper->getSecretKey()];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Response
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
            return array(
                "Response" => $response,
                "StatusCode" => $status_code
            );
    }
}
