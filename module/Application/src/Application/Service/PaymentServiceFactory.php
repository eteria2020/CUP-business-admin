<?php

namespace Application\Service;

use Application\Service\Factory\FirstPaymentFactory;
use Application\Service\Factory\PaymentFactory;
use Application\Service\Factory\TokenPaymentFactory;
use MvlabsPayments\Parameters;
use MvlabsPayments\Payments\FirstPayment;
use MvlabsPayments\Payments\Payment;
use MvlabsPayments\Payments\TokenPayment;
use Zend\EventManager\EventManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PaymentServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $eventManager = new EventManager('PaymentService');

        $parameters = $serviceLocator->get('Config')['omnipay'];
        $firstPaymentParameters = new Parameters($parameters['first-payment']);
        $tokenPaymentParameters = new Parameters($parameters['token-payment']);

        $firstPayment = FirstPaymentFactory::firstPayment($eventManager, $firstPaymentParameters);
        $tokenPayment = TokenPaymentFactory::tokenPayment($eventManager, $tokenPaymentParameters);
        $payment = PaymentFactory::payment($firstPayment, $tokenPayment);

        return new PaymentService($payment);
    }
}
