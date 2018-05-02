<?php

namespace Application\Controller;

use Application\Controller\Plugin\TranslatorPlugin;
use BusinessCore\Entity\Business;
use BusinessCore\Service\BusinessPaymentService;
use BusinessCore\Service\ExtraPaymentService;
//use BusinessCore\Service\DatatableService;
use BusinessCore\Service\SubscriptionService;
//use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * @method TranslatorPlugin translatorPlugin()
 */
class SubscriptionController extends AbstractActionController {

    /**
     * @var SubscriptionService
     */
    private $subscriptionService;

    /**
     * @var BusinessPaymentService
     */
    private $businessPaymentService;

    /**
     *
     * @var type 
     */
    private $extraPaymentService;

    /**
     * PaymentsController constructor.
     * @param SubscriptionService $subscriptionService
     * @param BusinessPaymentService $businessPaymentService
     */
    public function __construct(
    SubscriptionService $subscriptionService, BusinessPaymentService $businessPaymentService, ExtraPaymentService $extraPaymentService
    ) {
        $this->subscriptionService = $subscriptionService;
        $this->businessPaymentService = $businessPaymentService;
        $this->extraPaymentService = $extraPaymentService;
    }

    public function subscriptionAction() {
        /** @var Business $business */
        $business = $this->identity()->getBusiness();
        if ($business->payWithCreditCard() && !$business->hasActiveContract()) {
            $subscriptionPayment = $this->businessPaymentService->getBusinessSubscriptionPayment($business);
            $this->subscriptionService->paySubscription($subscriptionPayment);
        } else {
            $this->redirect()->toRoute('home');
        }
    }

    public function subscriptionPaymentConcludedAction() {
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

    public function subscriptionPaymentCancelledAction() {
        $params = $this->params()->fromQuery();

        $codTrans = $params['codTrans'];
        if (isset($codTrans)) {
            $this->subscriptionService->rejectedSubscriptionPayment($codTrans);
        }
        $this->flashMessenger()->addErrorMessage($this->translatorPlugin()->translate('Pagamento annullato'));
        $this->redirect()->toRoute('home');
    }

    /**
     * Action to change credit card contract.
     */
    public function creditCardChangeAction() {
        $business = $this->identity()->getBusiness();
        if ($business->payWithCreditCard()) {
            $extraPayment = $this->businessPaymentService->getBusinessExtraPaymentCreditCardChange($business);
            $this->extraPaymentService->payCreditCardChange($extraPayment);
        } else {
            $this->redirect()->toRoute('home');
        }
    }

}
