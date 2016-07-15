<?php

namespace Application\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PaymentListenerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $transactionService = $serviceLocator->get('BusinessCore\Service\TransactionService');
        $contractService = $serviceLocator->get('BusinessCore\Service\ContractService');
        return new PaymentListener($transactionService, $contractService);
    }
}
