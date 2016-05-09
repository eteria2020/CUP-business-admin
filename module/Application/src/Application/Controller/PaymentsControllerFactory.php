<?php
namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PaymentsControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface|ServiceLocatorAwareInterface $serviceLocator
     * @return PaymentsController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceManager = $serviceLocator->getServiceLocator();

        $businessPaymentService = $sharedServiceManager->get('BusinessCore\Service\BusinessPaymentService');
        $datatableService = $sharedServiceManager->get('BusinessCore\Service\DatatableService');

        return new PaymentsController($businessPaymentService, $datatableService);
    }
}
