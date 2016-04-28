<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use BusinessCore\Entity\Business;
use BusinessCore\Entity\BusinessInvoice;
use BusinessCore\Entity\Webuser;
use BusinessCore\Service\BusinessInvoiceService;
use BusinessCore\Service\BusinessService;
use BusinessCore\Service\DatatableService;
use BusinessCore\Service\InvoicePdfService;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use ZfcUser\Exception\AuthenticationEventException;

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
    ){
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
        $business = $this->getCurrentBusiness();
        $businessInvoice = $this->businessInvoiceService->findOneByIdAndBusiness($invoiceId, $business);
        return $this->invoicePdfService->generatePdfFromInvoice($businessInvoice->getInvoice());
    }

    /**
     * @return Business
     */
    private function getCurrentBusiness()
    {
        $user = $this->authService->getIdentity();
        if ($user instanceof Webuser) {
            return $user->getBusiness();
        } else {
            throw new AuthenticationEventException("Errore di autenticazione");
        }
    }

    public function datatableAction()
    {
        $filters = $this->params()->fromPost();
        $business = $this->getCurrentBusiness();
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
}
