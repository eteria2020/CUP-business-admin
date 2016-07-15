<?php

namespace Application\Service\Factory;

use MvlabsPayments\Parameters;
use MvlabsPayments\Payments\FirstPayment;
use Omnipay\Cartasi\Gateway;
use Zend\EventManager\EventManagerInterface;

class FirstPaymentFactory
{
    public static function firstPayment(EventManagerInterface $eventManager, Parameters $parameters)
    {
        $firstPaymentGateway = new Gateway();
        return new FirstPayment($eventManager, $firstPaymentGateway, $parameters);
    }
}
