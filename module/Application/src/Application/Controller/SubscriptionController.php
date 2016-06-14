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
     * PaymentsController constructor.
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(
        SubscriptionService $subscriptionService
    ) {
        $this->subscriptionService = $subscriptionService;
    }

    public function subscriptionAction()
    {
        /** @var Business $business */
        $business = $this->identity()->getBusiness();
        if ($this->getRequest()->isPost()) {
            $this->subscriptionService->paySubscription($business);
            if ($business->getPaymentType() == Business::TYPE_WIRE_TRANSFER) {
                $this->flashMessenger()->addSuccessMessage($this->translatorPlugin()->translate('Quota di iscrizione inserita nella lista pagamenti'));
            }
        }
        return new ViewModel([
            'business' => $business
        ]);
    }
}
