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

class Response extends \Magento\Framework\App\Action\Action
{
    protected $orderRepository;
    protected $_redirect;
    protected $_order,$_quote;
    protected $_checkoutSession;
    protected $_customerSession;
        /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $_invoiceService;
    protected $_invoiceSender;
    protected $_resultRedirect;
    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $_transaction;
    protected $_payment,$_orderFactory,$_quoteRepository,$_quoteManagement,$_orderRepository,$totalsCollector,$_registry;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        //\Magento\Paypal\Model\Express\Checkout\Factory $checkoutFactory,
        \Magento\Framework\Session\Generic $paypalSession,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Sales\Model\Order $order,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Api\CartManagementInterface $quoteManagement,
        \Magento\Checkout\Api\AgreementsValidatorInterface $agreementValidator,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->agreementsValidator = $agreementValidator;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_orderFactory = $orderFactory;
        $this->_order = $order;
        $this->_quote = $quote;
        $this->_redirect = $redirect;
        $this->_quoteRepository = $quoteRepository;
        $this->_quoteManagement = $quoteManagement;
        $this->_orderRepository = $orderRepository;
        $this->totalsCollector = $totalsCollector;
        $this->_invoiceService = $invoiceService;
        $this->_transaction = $transaction;
        $this->_invoiceSender = $invoiceSender;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
        parent::__construct($context);
        //$this->_getOrder();
    }

    public function execute()
    {
        $token = isset( $_GET['token'] ) ? $_GET['token'] : "";
        $amount = isset( $_GET['amount'] ) ? $_GET['amount'] : "";
        $validate = self::khalti_validate($token,$amount);
        $status_code = $validate['status_code'];
        $idx = $validate['idx'];
        //var_dump($validate);die();
        $order = $this->_checkoutSession->getLastRealOrder();
        $orderId = $this->_checkoutSession->getLastRealOrderId();
        $total = $order->getBaseGrandTotal()*100*100;
        //die($validate);
        //only enter this part if the last checkout session and khalti reutnred data are same
        if($amount=="$total" && $idx!=null)
        {
            $note = json_encode($validate);
            $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)
            ->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
            $order->save();

            if($order->canInvoice()) {
                //die("can invoice");
                $invoice = $this->_invoiceService->prepareInvoice($order);
                $invoice->register();
                $invoice->save();
                $transactionSave = $this->_transaction->addObject(
                    $invoice
                )->addObject(
                    $invoice->getOrder()
                );
                $transactionSave->save();
                $this->_invoiceSender->send($invoice);
                //send notification code
                $order->addStatusHistoryComment(
                    __($note)
                )
                ->setIsCustomerNotified(true)
                ->save();
                //die('Invoice Created');
            }
            //die('success');
            $RedirectUrl = $this->_url->getUrl('checkout/onepage/success');
            $this->_responseFactory->create()->setRedirect($RedirectUrl)->sendResponse();
            die();

        }
        else
        {
            $this->cancelOrder();
            $RedirectUrl = $this->_url->getUrl('checkout/onepage/failure');
            $this->_responseFactory->create()->setRedirect($RedirectUrl)->sendResponse();
            die();
        }

    }

    public function cancelOrder()
    {
        $this->_checkoutSession->getLastRealOrder()->cancel()->save();
        //f$this->_redirect->redirect($controller->getResponse(), 'my/custom/url');
    }

    public function khalti_validate($token,$amount)
    {
        $args = http_build_query(array(
            'token' => $token,
            'amount'  => $amount
           ));

        $url = "https://khalti.com/api/payment/verify/";

        # Make the call using API.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $headers = ['Authorization: Key test_secret_key_e93b7d5c980e4cb5a1eed6b3d4ba5068'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Response
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $response = json_decode($response);
        $idx = $response->idx;
        $data = array(
            "idx" => $idx,
            "status_code" => $status_code
        );
        curl_close($ch);
        return $data;
    }

    public function khalti_details($amt,$oid)
    {
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://khalti.com.np/epay/transdetails?amt='.$amt.'&scd=OLIZ&pid='.$oid,
        //CURLOPT_URL => 'http://www.magento.net/khalti/details.php?amt='.$amt.'&scd=OLIZ&pid='.$oid, //test
        CURLOPT_USERAGENT => 'Codular Sample cURL Request'
        ));
        // Send the request & save response to $resp
        $result = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        $subject = $result;
        $search = '{"code":"00","msg":"Success",';
        $trimmed = str_replace($search, '', $subject);
        $search = '}}';
        $final = str_replace($search, '}', $trimmed);
        return $final;
                    
    }

}
