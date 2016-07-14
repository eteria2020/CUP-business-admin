<?php
namespace Application\View\Helper;

use BusinessCore\Entity\Business;
use BusinessCore\Entity\SubscriptionPayment;
use BusinessCore\Service\BusinessPaymentService;
use Zend\View\Helper\AbstractHelper;

class BusinessInfoPanelHelper extends AbstractHelper
{
    /**
     * @var BusinessPaymentService
     */
    private $businessPaymentService;

    public function __construct(BusinessPaymentService $businessPaymentService)
    {
        $this->businessPaymentService = $businessPaymentService;
    }

    public function __invoke(Business $business)
    {

        $subscriptionPayment = $this->businessPaymentService->getBusinessSubscriptionPayment($business);

        if (!$subscriptionPayment instanceof SubscriptionPayment) {
            return '';
        }

        $html = '<div class="row"><div class="col-lg-12"><div>'
            . $this->getView()->translate("Stato abilitazione") . '</div>';
        if ($business->isEnabled()) {
            $html .= '<div class="alert alert-success">' . $this->getView()->translate("Abilitata");
        } else {
            $html .= '<div class="alert alert-danger">' . $this->getView()->translate("Non abilitata");
        }
        $html .= '</div></div></div>';

        $html .= '<div class="row"><div class="col-lg-12"><div>'
            . $this->getView()->translate("Pagamento iscrizione") . '</div>';
        if ($subscriptionPayment->isPayed()) {
            $text = $this->getView()->translate("Pagata in data %s");
            $html .= '<div class="alert alert-success">'
                . sprintf($text, $subscriptionPayment->getPayedOnTs()->format("d-m-Y H:i:s"));
        } elseif ($subscriptionPayment->isExpectedPayed()) {
            $text = $this->getView()->translate("In attesa di conferma da parte di sharengo");
            $html .= '<div class="alert alert-info">' . $text;
        } else {
            $text = $this->getView()->translate("Per attivare l'azienda devi pagare la quota di iscrizione di euro %s");
            $html .= '<div class="alert alert-warning">' . sprintf($text, $business->getReadableSubscriptionFee());
            if ($business->payWithCreditCard()) {

                $html .= '<form method="post"><input class="btn btn-success" type="submit" value="'
                    . $this->getView()->translate("Paga con carta di credito")
                    . '"></form>';
            } else {

                $html .= '<br>' . $this->getView()->translate("Una volta pagato l'importo attraverso bonifico bancario, vai nel menu pagamenti e segnalalo come pagato ");
            }
            $html .= '</div></div></div>';
        }

        $html .= '</div></div></div>';
        return $html;
    }
}
