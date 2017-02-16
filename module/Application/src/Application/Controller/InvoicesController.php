<?php
namespace Application\Controller;

use Application\Controller\Plugin\TranslatorPlugin;
use BusinessCore\Entity\BusinessInvoice;
use BusinessCore\Service\BusinessInvoiceService;
use BusinessCore\Service\DatatableService;
use BusinessCore\Service\PdfService;

use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * @method TranslatorPlugin translatorPlugin()
 */
class InvoicesController extends AbstractActionController
{
    /**
     * @var DatatableService
     */
    private $datatableService;
    /**
     * @var BusinessInvoiceService
     */
    private $businessInvoiceService;
    /**
     * @var PdfService
     */
    private $pdfService;

    /**
     * EmployeesController constructor.
     * @param BusinessInvoiceService $businessInvoiceService
     * @param PdfService $pdfService
     * @param DatatableService $datatableService
     */
    public function __construct(
        BusinessInvoiceService $businessInvoiceService,
        PdfService $pdfService,
        DatatableService $datatableService
    ) {
        $this->businessInvoiceService = $businessInvoiceService;
        $this->pdfService = $pdfService;
        $this->datatableService = $datatableService;
    }

    public function invoicesAction()
    {
        return new ViewModel();
    }

    public function pdfAction()
    {
        $invoiceId = $this->params()->fromRoute('id', 0);
        $business = $this->identity()->getBusiness();
        $businessInvoice = $this->businessInvoiceService->findOneByIdAndBusiness($invoiceId, $business);
        return $this->generatePdfResponse($businessInvoice);
    }

    public function datatableAction()
    {
        $filters = $this->params()->fromPost();
        $business = $this->identity()->getBusiness();
        $searchCriteria = $this->datatableService->getSearchCriteria($filters);
        $businessInvoices = $this->businessInvoiceService->searchInvoicesByBusiness($business, $searchCriteria);
        $businessInvoicesNumber = $this->businessInvoiceService->countFilteredInvoicesByBusiness($business, $searchCriteria);
        $dataDataTable = $this->mapBusinessInvoicesToDatatable($businessInvoices);
        $totalInvoices = $this->businessInvoiceService->getTotalInvoicesByBusiness($business);

        return new JsonModel([
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $totalInvoices,
            'recordsFiltered' => $businessInvoicesNumber,
            'data'            => $dataDataTable
        ]);
    }

    private function mapBusinessInvoicesToDatatable(array $businessInvoices)
    {
        return array_map(function (BusinessInvoice $businessInvoice) {

            return [
                'bi' => [
                    'id' => $businessInvoice->getId(),
                    'invoiceNumber' => $businessInvoice->getInvoiceNumber(),
                    'invoiceDate' => $businessInvoice->getInvoiceDate(),
                    'type' => $this->formatInvoiceType($businessInvoice->getType()),
                    'amount' => $businessInvoice->getAmount(),
                ],
            ];
        }, $businessInvoices);
    }

    private function formatInvoiceType($paymentType)
    {
        switch ($paymentType) {
            case BusinessInvoice::TYPE_TIME_PACKAGE:
                return $this->translatorPlugin()->translate("Pacchetto minuti");
            case BusinessInvoice::TYPE_EXTRA:
                return $this->translatorPlugin()->translate("Extra / Penale");
            case BusinessInvoice::TYPE_TRIP:
                return $this->translatorPlugin()->translate("Corsa");
            case BusinessInvoice::TYPE_SUBSCRIPTION:
                return $this->translatorPlugin()->translate("Sottoscrizione");
            default:
                return $paymentType;
        }
    }

    private function generatePdfResponse(BusinessInvoice $invoice)
    {
        $pdf = $this->pdfService->generatePdfFromInvoice($invoice);
        $response = new Response();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/pdf');
        $headers->addHeaderLine(
            'Content-Disposition',
            "attachment; filename=\"Fattura-" . $invoice->getInvoiceNumber() . ".pdf\""
        );
        $headers->addHeaderLine('Content-Length', strlen($pdf));

        $response->setContent($pdf);

        return $response;
    }
}
