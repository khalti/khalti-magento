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

namespace Fourwallsinn\Khalti\Controller\Response;

use Fourwallsinn\Khalti\Helper\Data as KhaltiHelper;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\DB\Transaction;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\Serialize\Serializer\Json;

class Response extends Action
{
    /**
     * @var Order
     */
    protected $_order;
    /**
     * @var InvoiceService
     */
    protected $_invoiceService;
    /**
     * @var InvoiceSender
     */
    protected $_invoiceSender;
    /**
     * @var Transaction
     */
    protected $_transaction;
    /**
     * @var ResponseFactory
     */
    private $_responseFactory;
    /**
     * @var KhaltiHelper
     */
    private $khaltiHelper;
    /**
     * @var Json
     */
    protected $jsonSerializer;
    /**
     * @var CheckoutSession
     */
    private $_checkoutSession;

    /**
     * Response constructor.
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     * @param Order $order
     * @param InvoiceService $invoiceService
     * @param InvoiceSender $invoiceSender
     * @param Transaction $transaction
     * @param ResponseFactory $responseFactory
     * @param UrlInterface $url
     * @param KhaltiHelper $paymentHelper
     * @param Json $jsonSerializer
     */
    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        Order $order,
        InvoiceService $invoiceService,
        InvoiceSender $invoiceSender,
        Transaction $transaction,
        ResponseFactory $responseFactory,
        UrlInterface $url,
        KhaltiHelper $paymentHelper,
        Json $jsonSerializer
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_order = $order;
        $this->_invoiceService = $invoiceService;
        $this->_transaction = $transaction;
        $this->_invoiceSender = $invoiceSender;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
        $this->khaltiHelper = $paymentHelper;
        $this->jsonSerializer = $jsonSerializer;
        parent::__construct($context);
    }

    public function execute()
    {
        $token = $this->getRequest()->getParam('token');
        $amount = $this->getRequest()->getParam('amount');
        $validate = $this->khalti_validate($token,$amount);

        $status_code = $validate['status_code'];
        $idx = $validate['idx'];

        $order = $this->_checkoutSession->getLastRealOrder();
        $total = $order->getBaseGrandTotal()*100;

        if($amount=="$total" && $idx!=null && $status_code == 200)
        {
            $note = $this->jsonSerializer->serialize($validate);
            $order->setState(Order::STATE_PROCESSING)
            ->setStatus(Order::STATE_PROCESSING);
            $order->save();

            if($order->canInvoice()) {

                $invoice = $this->_invoiceService->prepareInvoice($order);
                $invoice->register();
                $invoice->save();
                $transactionSave = $this->_transaction
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());
                $transactionSave->save();
                $this->_invoiceSender->send($invoice);

                $order->addStatusHistoryComment(__($note))
                    ->setIsCustomerNotified(true)
                    ->save();
            }
            $RedirectUrl = $this->_url->getUrl('checkout/onepage/success');
            $this->_responseFactory
                ->create()
                ->setRedirect($RedirectUrl)
                ->sendResponse();
            return;

        } else {
            $this->cancelOrder();
            $RedirectUrl = $this->_url->getUrl('checkout/onepage/failure');
            $this->_responseFactory
                ->create()
                ->setRedirect($RedirectUrl)
                ->sendResponse();
        }
        return;
    }

    public function cancelOrder()
    {
        $this->_checkoutSession->getLastRealOrder()->cancel()->save();
    }

    public function khalti_validate($token,$amount)
    {
        $args = http_build_query(array(
            'token' => $token,
            'amount'  => $amount
           ));

        $url = "https://khalti.com/api/v2/payment/verify/";

        # Make the call using API.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $headers = ['Authorization: Key '.$this->khaltiHelper->getSecretKey()];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Response
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $response = json_decode($response);

        $idx = @$response->idx;
        $data = array(
            "idx" => $idx,
            "status_code" => $status_code,
            "response" => $response
        );
        curl_close($ch);
        return $data;
    }

}
