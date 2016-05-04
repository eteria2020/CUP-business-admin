<?php

namespace Application\Controller;

use BusinessCore\Entity\BusinessInvoice;
use BusinessCore\Entity\Invoice;
use BusinessCore\Entity\Webuser;
use BusinessCore\Service\BusinessInvoiceService;
use BusinessCore\Service\DatatableService;
use BusinessCore\Service\InvoicePdfService;

use Zend\Authentication\AuthenticationService;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use ZfcUser\Exception\AuthenticationEventException;

class InvoicesController extends AbstractActionController
{
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
     * @param BusinessInvoiceService $businessInvoiceService
     * @param InvoicePdfService $invoicePdfService
     * @param DatatableService $datatableService
     * @param AuthenticationService $authService
     */
    public function __construct(
        BusinessInvoiceService $businessInvoiceService,
        InvoicePdfService $invoicePdfService,
        DatatableService $datatableService,
        AuthenticationService $authService
    ) {
        $this->businessInvoiceService = $businessInvoiceService;
        $this->invoicePdfService = $invoicePdfService;
        $this->datatableService = $datatableService;
        $this->authService = $authService;
    }

    public function invoicesAction()
    {
        return new ViewModel();
    }

    public function pdfAction()
    {
        $invoiceId = $this->params()->fromRoute('id', 0);
        $business = $this->retrieveAuthenticatedUser()->getBusiness();
        $businessInvoice = $this->businessInvoiceService->findOneByIdAndBusiness($invoiceId, $business);
        return $this->generatePdfResponse($businessInvoice->getInvoice());
    }

    public function datatableAction()
    {
        $filters = $this->params()->fromPost();
        $business = $this->retrieveAuthenticatedUser()->getBusiness();
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
            $invoice = $businessInvoice->getInvoice();
            $employee = $invoice->getEmployee();
            return [
                'i' => [
                    'id' => $invoice->getId(),
                    'invoiceNumber' => $invoice->getInvoiceNumber(),
                    'invoiceDate' => $invoice->getInvoiceDate(),
                    'type' => $invoice->getType(),
                    'amount' => $invoice->getAmount(),
                ],
                'e' => [
                    'name' => $employee->getName(),
                    'surname' => $employee->getSurname(),
                ]
            ];
        }, $businessInvoices);
    }

    private function generatePdfResponse(Invoice $invoice)
    {
        $pdf = $this->invoicePdfService->generatePdfFromInvoice($invoice);
        $response = new Response();
        $headers  = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/pdf');
        $headers->addHeaderLine(
            'Content-Disposition',
            "attachment; filename=\"Fattura-" . $invoice->getInvoiceNumber() . ".pdf\""
        );
        $headers->addHeaderLine('Content-Length', strlen($pdf));

        $response->setContent($pdf);

        return $response;
    }

    /**
     * @return Webuser
     */
    private function retrieveAuthenticatedUser()
    {
        $user = $this->authService->getIdentity();
        if ($user instanceof Webuser) {
            return $user;
        } else {
            throw new AuthenticationEventException($this->translatorPlugin()->translate("Errore di autenticazione"));
        }
    }
}
