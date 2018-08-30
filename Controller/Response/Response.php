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
        \Magento\Framework\UrlInterface $url,
        KhaltiHelper $paymentHelper
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
        $this->khaltiHelper = $paymentHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        $token = isset( $_GET['token'] ) ? $_GET['token'] : "";
        $amount = isset( $_GET['amount'] ) ? $_GET['amount'] : "";
        $validate = $this->khalti_validate($token,$amount);

        $status_code = $validate['status_code'];
        $idx = $validate['idx'];

        $order = $this->_checkoutSession->getLastRealOrder();
        $orderId = $this->_checkoutSession->getLastRealOrderId();
        $total = $order->getBaseGrandTotal()*100;

        if($amount=="$total" && $idx!=null && $status_code == 200)
        {
            $note = json_encode($validate);
            $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)
            ->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
            $order->save();

            if($order->canInvoice()) {

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

                $order->addStatusHistoryComment(
                    __($note)
                )
                ->setIsCustomerNotified(true)
                ->save();
            }
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
