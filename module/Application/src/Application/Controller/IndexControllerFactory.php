<?php
namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IndexControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface|ServiceLocatorAwareInterface $serviceLocator
     * @return IndexController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceManager = $serviceLocator->getServiceLocator();
        $businessSubscriptionService = $sharedServiceManager->get('BusinessCore\Service\SubscriptionService');
        $businessPaymentService = $sharedServiceManager->get('BusinessCore\Service\BusinessPaymentService');

        return new IndexController($businessSubscriptionService, $businessPaymentService);
    }
}