<?php

namespace Application\View\Helper;

use BusinessCore\Entity\Business;
use BusinessCore\Entity\SubscriptionPayment;
use BusinessCore\Service\BusinessPaymentService;
use Zend\View\Helper\AbstractHelper;

class BusinessInfoPanelHelper extends AbstractHelper {

    /**
     * @var BusinessPaymentService
     */
    private $businessPaymentService;

    public function __construct(BusinessPaymentService $businessPaymentService) {
        $this->businessPaymentService = $businessPaymentService;
    }

    public function __invoke(Business $business) {
        $html = '<div class="row"><div class="col-lg-12">';
        $html .= '<div>' . $this->getView()->translate("Codice di associazione") . '</div>';
        $html .= '<div class="alert alert-success">' . $business->getCode() . '</div>';
        $html .= '</div></div>';

        $subscriptionPayment = $this->businessPaymentService->getBusinessSubscriptionPayment($business);

        if (!$subscriptionPayment instanceof SubscriptionPayment) {
            return $html;
        }

        $html .= '<div class="row">' .
                '<div class="col-lg-12">';
        $html .= '<div>' . $this->getView()->translate("Stato abilitazione") . '</div>';

        if ($business->isEnabled()) {
            $html .= '<div class="alert alert-success">' . $this->getView()->translate("Abilitata") . '</div>';
        } else {
            $html .= '<div class="alert alert-danger">' . $this->getView()->translate("Non abilitata") . '</div>';
        }
        $html .= '</div></div>';

        $html .= '<div class="row"><div class="col-lg-12">';
        $html .= '<div>' . $this->getView()->translate("Pagamento iscrizione") . '</div>';

        if ($subscriptionPayment->isPayed()) {

            $html .= sprintf('<div class="alert alert-success col-sm-12">%s %s',
                    $this->getView()->translate("Pagata in data "),
                    $subscriptionPayment->getPayedOnTs()->format("d-m-Y H:i:s"));

            if ($business->payWithCreditCard() ) {
                $html .= sprintf('<br><br><a href="%s" /> <input class="btn btn-success col-sm-12" type="submit" value="%s" > </a>', 
                        $this->getView()->url('credit_card_change'),
                        $this->getView()->translate("Cambia carta"));
            }
            $html .= '</div>';
        } elseif ($subscriptionPayment->isExpectedPayed()) {
            $html .= '<div class="alert alert-info">' . $this->getView()->translate("In attesa di conferma da parte di sharengo") .'</div>';
        } else {
            $html .= '<div class="alert alert-warning col-sm-12" style="text-align: justify">';
            if ($business->payWithCreditCard() && !$business->hasActiveContract()) {
                 $html .= sprintf('%s %s &euro;.', 
                         $this->getView()->translate("Per attivare l'azienda devi pagare la quota di iscrizione di "),
                         $business->getReadableSubscriptionFee());

                 $html .= sprintf('<br><br><a href="%s" /> <input class="btn btn-success col-sm-12" type="submit" value="%s"> </a>',
                         $this->getView()->url('subscription'),
                         $this->getView()->translate("Paga con carta di credito")
                         );
            } else {
                $html .= '<br>' . $this->getView()->translate("Una volta pagato l'importo attraverso bonifico bancario, vai nel menu pagamenti e segnalalo come pagato.");
            }

            $html .= '</div>';
        }

        return $html;
    }

}
