<?php
namespace Application\Controller;

use Application\Controller\Plugin\TranslatorPlugin;
use BusinessCore\Entity\BusinessPayment;
use BusinessCore\Service\BusinessPaymentService;

use BusinessCore\Service\DatatableService;
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
     * PaymentsController constructor.
     * @param BusinessPaymentService $businessPaymentService
     * @param DatatableService $datatableService
     */
    public function __construct(
        BusinessPaymentService $businessPaymentService,
        DatatableService $datatableService
    ) {
        $this->businessPaymentService = $businessPaymentService;
        $this->datatableService = $datatableService;
    }

    public function paymentsAction()
    {
        $business = $this->identity()->getBusiness();
        $payments = $this->businessPaymentService->findAll($business);
        return new ViewModel([
            'business' => $business,
            'payments' => $payments
        ]);
    }

    public function creditCardAction()
    {
        return new ViewModel([
            'business' => $this->identity()->getBusiness()
        ]);
    }

    public function datatableAction()
    {
        $filters = $this->params()->fromPost();
        $business = $this->identity()->getBusiness();
        $searchCriteria = $this->datatableService->getSearchCriteria($filters);
        $businessPayments = $this->businessPaymentService->searchPaymentsByBusiness($business, $searchCriteria);
        $dataDataTable = $this->mapBusinessPaymentsToDatatable($businessPayments);
        $totalPayments = $this->businessPaymentService->getTotalPaymentsByBusiness($business);

        return new JsonModel([
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $totalPayments,
            'recordsFiltered' => count($dataDataTable),
            'data'            => $dataDataTable
        ]);
    }

    private function mapBusinessPaymentsToDatatable(array $businessPayments)
    {
        return array_map(function (BusinessPayment $businessPayment) {
            return [
                'bp' => [
                    'createdTs' => $businessPayment->getCreatedTs()->format('d-m-Y H:i:s'),
                    'type' => $this->formatPaymentType($businessPayment->getType()),
                    'amount' => $this->formatAmount($businessPayment->getAmount(), $businessPayment->getCurrency()),
                    'payedOnTs' => $this->formatStatus($businessPayment->getPayedOnTs()),
                ]
            ];
        }, $businessPayments);
    }

    private function formatPaymentType($paymentType)
    {
        switch ($paymentType) {
            case BusinessPayment::TIME_PACKAGE_TYPE:
                return $this->translatorPlugin()->translate("Pacchetto minuti");
                break;
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
        }
        return sprintf("%s %s", number_format($amount / 100, 2, '.', ''), $currencySymbol);
    }

    private function formatStatus(\DateTime $payedOnTs = null)
    {
        if (is_null($payedOnTs)) {
            return $this->translatorPlugin()->translate("Non pagato");
        } else {
            return sprintf($this->translatorPlugin()->translate("Pagato in data %s"), $payedOnTs->format('d-m-Y'));
        }
    }
}
