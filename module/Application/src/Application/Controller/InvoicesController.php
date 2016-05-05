<?php

namespace Application\Controller;

use BusinessCore\Entity\BusinessInvoice;
use BusinessCore\Service\BusinessInvoiceService;
use BusinessCore\Service\DatatableService;
use BusinessCore\Service\InvoicePdfService;
use Zend\Authentication\AuthenticationService;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class InvoicesController extends AbstractActionController
{
    /**
     * @var Translator
     */
    private $translator;
    /**
     * @var AuthenticationService
     */
    private $authService;
    /**
     * @var DatatableService
     */
    private $datatableService;
    /**
     * @var BusinessInvoiceService
     */
    private $businessInvoiceService;
    /**
     * @var InvoicePdfService
     */
    private $invoicePdfService;

    /**
     * EmployeesController constructor.
     * @param Translator $translator
     * @param BusinessInvoiceService $businessInvoiceService
     * @param InvoicePdfService $invoicePdfService
     * @param DatatableService $datatableService
     * @param AuthenticationService $authService
     */
    public function __construct(
        Translator $translator,
        BusinessInvoiceService $businessInvoiceService,
        InvoicePdfService $invoicePdfService,
        DatatableService $datatableService,
        AuthenticationService $authService
    ) {
        $this->translator = $translator;
        $this->datatableService = $datatableService;
        $this->authService = $authService;
        $this->businessInvoiceService = $businessInvoiceService;
        $this->invoicePdfService = $invoicePdfService;
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
        $dataDataTable = $this->mapBusinessInvoicesToDatatable($businessInvoices);
        $totalInvoices = $this->businessInvoiceService->getTotalInvoicesByBusiness($business);

        return new JsonModel([
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $totalInvoices,
            'recordsFiltered' => count($dataDataTable),
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
                    'type' => $businessInvoice->getType(),
                    'amount' => $businessInvoice->getAmount(),
                ],
            ];
        }, $businessInvoices);
    }

    private function generatePdfResponse(BusinessInvoice $invoice)
    {
        $pdf = $this->invoicePdfService->generatePdfFromInvoice($invoice);
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
