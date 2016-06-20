<?php
namespace Application\Controller;

use Application\Controller\Plugin\TranslatorPlugin;
use BusinessCore\Entity\Base\BusinessPayment;
use BusinessCore\Entity\Business;
use BusinessCore\Entity\BusinessTripPayment;
use BusinessCore\Entity\ExtraPayment;
use BusinessCore\Entity\SubscriptionPayment;
use BusinessCore\Entity\TimePackagePayment;
use BusinessCore\Service\BusinessPaymentService;

use BusinessCore\Service\DatatableService;
use BusinessCore\Service\PdfService;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * @method TranslatorPlugin translatorPlugin()
 */
class PaymentsController extends AbstractActionController
{
    /**
     * @var BusinessPaymentService
     */
    private $businessPaymentService;
    /**
     * @var DatatableService
     */
    private $datatableService;
    /**
     * @var PdfService
     */
    private $pdfService;

    /**
     * PaymentsController constructor.
     * @param BusinessPaymentService $businessPaymentService
     * @param DatatableService $datatableService
     * @param PdfService $pdfService
     */
    public function __construct(
        BusinessPaymentService $businessPaymentService,
        DatatableService $datatableService,
        PdfService $pdfService
    ) {
        $this->businessPaymentService = $businessPaymentService;
        $this->datatableService = $datatableService;
        $this->pdfService = $pdfService;
    }

    public function paymentsAction()
    {
        return new ViewModel();
    }

    public function datatableAction()
    {
        $filters = $this->params()->fromPost();
        $business = $this->identity()->getBusiness();
        $totalPayments = $this->businessPaymentService->getTotalPaymentsByBusiness($business);
        $searchCriteria = $this->datatableService->getSearchCriteria($filters);
        $businessPaymentsNumber = $this->businessPaymentService->countFilteredPaymentsByBusiness($business, $searchCriteria);
        $businessPayments = $this->businessPaymentService->searchPaymentsByBusiness($business, $searchCriteria);
        $dataDataTable = $this->mapBusinessPaymentsToDatatable($businessPayments);

        $reportData = $this->businessPaymentService->getReportData($business, $searchCriteria);
        $reportTotal = $this->businessPaymentService->getReportTotal($business, $searchCriteria);

        $parsedReportData = $this->mapBusinessPaymentsToReport($reportData, $reportTotal);

        return new JsonModel([
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $totalPayments,
            'recordsFiltered' => $businessPaymentsNumber,
            'data'            => $dataDataTable,
            'reportData'      => $parsedReportData
        ]);
    }

    public function flagAsPayedAction()
    {
        $type = $this->params()->fromRoute('type', 0);
        $id = $this->params()->fromRoute('id', 0);

        $className = null;
        switch ($type) {
            case BusinessPayment::TYPE_TRIP:
                $className = BusinessTripPayment::CLASS_NAME;
                break;
            case BusinessPayment::TYPE_PACKAGE:
                $className = TimePackagePayment::CLASS_NAME;
                break;
            case BusinessPayment::TYPE_EXTRA:
                $className = ExtraPayment::CLASS_NAME;
                break;
            case BusinessPayment::TYPE_SUBSCRIPTION:
                $className = SubscriptionPayment::CLASS_NAME;
                break;
            default:
                throw new \Exception;
        }
        $this->businessPaymentService->flagPaymentAsExpectedPayedByWire($className, $id);
        return $this->redirect()->toRoute('payments');
    }

    public function downloadReportAction()
    {
        $post = $this->params()->fromPost();

        $reportData = json_decode($post['data'], true);
        return $this->generatePdfResponse($reportData);
    }

    private function mapBusinessPaymentsToDatatable(array $businessPayments)
    {
        return array_map(function ($businessPayment) {

            $payedOn = empty($businessPayment['payed_on_ts']) ? '-' : date_create($businessPayment['payed_on_ts'])->format('d-m-Y H:i:s');
            return [
                'created_ts' => date_create($businessPayment['created_ts'])->format('d-m-Y H:i:s'),
                'type' => $this->formatPaymentType($businessPayment['type']),
                'amount' => $this->formatAmount($businessPayment['amount'], $businessPayment['currency']),
                'payed_on_ts' => $payedOn,
                'status' => $this->formatStatus($businessPayment['status']),
                'details' => $this->formatAdditionalDetails($businessPayment)
            ];
        }, $businessPayments);
    }

    private function mapBusinessPaymentsToReport(array $businessPayments, array $totals)
    {
        $payments['payments'] = array_map(function ($businessPayment) {

            $payedOn = empty($businessPayment['payed_on_ts']) ? '-' : date_create($businessPayment['payed_on_ts'])->format('d-m-Y H:i:s');
            return [
                'created_ts' => date_create($businessPayment['created_ts'])->format('d-m-Y H:i:s'),
                'type' => $this->formatPaymentType($businessPayment['type']),
                'amount' => $this->formatAmount($businessPayment['amount'], $businessPayment['currency']),
                'payed_on_ts' => $payedOn,
                'status' => $this->formatStatus($businessPayment['status']),
            ];
        }, $businessPayments);

        if (empty($totals)) {
            $payments['totals'][] = '0';
        } else {
            foreach ($totals as $total) {
                $payments['totals'][] = $this->formatAmount($total['total'], $total['currency']);
            }
        }

        return $payments;
    }

    private function formatPaymentType($paymentType)
    {
        switch ($paymentType) {
            case BusinessPayment::TYPE_PACKAGE:
                return $this->translatorPlugin()->translate("Pacchetto minuti");
            case BusinessPayment::TYPE_EXTRA:
                return $this->translatorPlugin()->translate("Extra / Penale");
            case BusinessPayment::TYPE_TRIP:
                return $this->translatorPlugin()->translate("Corsa");
            case BusinessPayment::TYPE_SUBSCRIPTION:
                return $this->translatorPlugin()->translate("Sottoscrizione");
            default:
                return $paymentType;
        }
    }

    private function formatAmount($amount, $currency)
    {
        $currencySymbol = $currency;
        switch ($currency) {
            case 'EUR':
                $currencySymbol = "€";
                break;
        }
        return sprintf("%s %s", number_format($amount / 100, 2, '.', ''), $currencySymbol);
    }

    private function formatStatus($status)
    {
        switch ($status) {
            case BusinessPayment::STATUS_CONFIRMED_PAYED:
                return $this->translatorPlugin()->translate("Pagato");
            case BusinessPayment::STATUS_EXPECTED_PAYED:
                return $this->translatorPlugin()->translate("Pagato, in attesa di conferma");
            case BusinessPayment::STATUS_INVOICED:
                return $this->translatorPlugin()->translate("Pagato e fatturato");
            case BusinessPayment::STATUS_PENDING:
                return $this->translatorPlugin()->translate("Non pagato");
        }
        return $status;
    }

    private function formatAdditionalDetails($businessPayment)
    {
        $invoiceId = $businessPayment['invoice_id'];
        if (!empty($invoiceId)) {
            $url = $this->url()->fromRoute('invoices/pdf', ['id' => $invoiceId]);
            $text = $this->translatorPlugin()->translate("Download fattura");
            return sprintf("<a href=%s>%s</a>", $url, $text);
        }

        /** @var Business $business */
        $business = $this->identity()->getBusiness();
        if ($business->getPaymentType() == Business::TYPE_WIRE_TRANSFER) {
            $status = $businessPayment['status'];
            if ($status == BusinessPayment::STATUS_PENDING) {
                $type = $businessPayment['type'];
                $paymentId = $businessPayment['payment_id'];
                $url = $this->url()->fromRoute('payments/flag-as-payed', ['type' => $type, 'id' => $paymentId]);
                $text = $this->translatorPlugin()->translate("Segna come pagata");
                return sprintf("<a href=%s>%s</a>", $url, $text);
            }
        }
        return '-';
    }

    private function generatePdfResponse(array $reportData)
    {
        $pdf = $this->pdfService->generatePaymentReport($reportData);
        $response = new Response();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/pdf');
        $headers->addHeaderLine(
            'Content-Disposition',
            "attachment; filename=\"Report.pdf\""
        );
        $headers->addHeaderLine('Content-Length', strlen($pdf));

        $response->setContent($pdf);

        return $response;
    }
}
