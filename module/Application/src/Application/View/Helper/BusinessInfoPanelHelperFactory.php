<?php
namespace Application\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BusinessInfoPanelHelperFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return BusinessInfoPanelHelper
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceManager = $serviceLocator->getServiceLocator();
        $businessPaymentService = $sharedServiceManager->get('BusinessCore\Service\BusinessPaymentService');

        return new BusinessInfoPanelHelper($businessPaymentService);
    }
}
