<?php
namespace Application\View\Helper;

use BusinessCore\Entity\Business;
use Zend\View\Helper\AbstractHelper;

class ParamsHelper extends AbstractHelper
{
    public function displayPaymentType(Business $business)
    {
        $type = $business->getPaymentType();
        switch ($type) {
            case Business::TYPE_CREDIT_CARD:
                return $this->getView()->translate("Carta di credito");
            case Business::TYPE_WIRE_TRANSFER:
                return $this->getView()->translate("Bonifico bancario");
        }
        return $type;
    }

    public function displayPaymentFrequence(Business $business)
    {
        $frequence = $business->getPaymentFrequence();
        switch ($frequence) {
            case Business::FREQUENCE_WEEKLY:
                return $this->getView()->translate("Settimanale");
            case Business::FREQUENCE_FORTNIGHTLY:
                return $this->getView()->translate("Quindicinale");
            case Business::FREQUENCE_MONTHLY:
                return $this->getView()->translate("Mensile");
        }
        return $frequence;
    }
}
