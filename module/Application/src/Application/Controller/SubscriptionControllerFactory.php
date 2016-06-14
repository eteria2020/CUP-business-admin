<?php
namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SubscriptionControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface|ServiceLocatorAwareInterface $serviceLocator
     * @return SubscriptionController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceManager = $serviceLocator->getServiceLocator();

        $businessPaymentService = $sharedServiceManager->get('BusinessCore\Service\BusinessPaymentService');
        $subscriptionService = $sharedServiceManager->get('BusinessCore\Service\SubscriptionService');

        return new SubscriptionController($subscriptionService);
    }
}
