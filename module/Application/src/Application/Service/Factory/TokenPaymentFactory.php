<?php

namespace Application\Service\Factory;

use MvlabsPayments\Parameters;
use MvlabsPayments\Payments\TokenPayment;
use Omnipay\Cartasi\TokenGateway;
use Zend\EventManager\EventManagerInterface;

class TokenPaymentFactory
{
    public static function tokenPayment(EventManagerInterface $eventManager, Parameters $parameters)
    {
        $tokenPaymentGateway = new TokenGateway();
        return new TokenPayment($eventManager, $tokenPaymentGateway, $parameters);
    }
}
