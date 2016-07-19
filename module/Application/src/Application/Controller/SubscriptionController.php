<?php
namespace Application\Controller;

use Application\Controller\Plugin\TranslatorPlugin;
use BusinessCore\Entity\Base\BusinessPayment;
use BusinessCore\Entity\Business;
use BusinessCore\Entity\BusinessTripPayment;
use BusinessCore\Entity\ExtraPayment;
use BusinessCore\Entity\TimePackagePayment;
use BusinessCore\Service\BusinessPaymentService;

use BusinessCore\Service\DatatableService;
use BusinessCore\Service\PdfService;
use BusinessCore\Service\SubscriptionService;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * @method TranslatorPlugin translatorPlugin()
 */
class SubscriptionController extends AbstractActionController
{
    /**
     * @var SubscriptionService
     */
    private $subscriptionService;
    /**
     * @var BusinessPaymentService
     */
    private $businessPaymentService;

    /**
     * PaymentsController constructor.
     * @param SubscriptionService $subscriptionService
     * @param BusinessPaymentService $businessPaymentService
     */
    public function __construct(
        SubscriptionService $subscriptionService,
        BusinessPaymentService $businessPaymentService
    ) {
        $this->subscriptionService = $subscriptionService;
        $this->businessPaymentService = $businessPaymentService;
    }

    public function subscriptionAction()
    {
        $business = $this->identity()->getBusiness();
        if ($business->payWithCreditCard()) {
            $subscriptionPayment = $this->businessPaymentService->getBusinessSubscriptionPayment($business);
            $this->subscriptionService->paySubscription($subscriptionPayment);
        }
    }

    public function subscriptionPaymentConcludedAction()
    {
        $params = $this->params()->fromQuery();
        $isSuccess = $this->subscriptionService->concludedSubscriptionPayment($params);
        if ($isSuccess) {
            $message = $this->translatorPlugin()->translate('Pagamento concluso');
            $this->flashMessenger()->addSuccessMessage($message);
        } else {
            $message = $this->translatorPlugin()->translate('Si è verificato un problema, non è stato possibile concludere il pagamento');
            $this->flashMessenger()->addErrorMessage($message);
        }

        $this->redirect()->toRoute('home');
    }

    public function subscriptionPaymentCancelledAction()
    {
        $params = $this->params()->fromQuery();
        $this->subscriptionService->rejectedSubscriptionPayment($params);
        $this->flashMessenger()->addErrorMessage($this->translatorPlugin()->translate('Pagamento annullato'));
        $this->redirect()->toRoute('home');
    }
}
