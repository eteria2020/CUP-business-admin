<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use BjyAuthorize\View\RedirectionStrategy;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Zend\Validator\AbstractValidator;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $application = $e->getApplication();
        $eventManager = $application->getEventManager();
        $serviceManager = $application->getServiceManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $options = $serviceManager->get('zfcuser_module_options');

        
        // BjyAuthorize redirection strategy
        $strategy = new RedirectionStrategy();

        // Change default layout if user is not logged
        $auth = $serviceManager->get('zfcuser_auth_service');
        if (!$auth->hasIdentity()) {
            $eventManager->attach("dispatch", function ($e) {
                $I_controller = $e->getTarget();
                $I_controller->layout('layout/layout_login');
            });

        } else {
            // if user is not logged, set unauthorized route name
            $strategy->setRedirectRoute('unauthorized');
        }

        $eventManager->attach($strategy);

        $translator     = $serviceManager->get('translator');
        $translator->addTranslationFile(
            'phpArray',
            'vendor/zendframework/zendframework/resources/languages/it/Zend_Validate.php',
            'default',
            'it_IT'
        );

        AbstractValidator::setDefaultTranslator($translator);

        // Add ACL information to Navigation view helper
        $authorize = $serviceManager->get('BjyAuthorize\Service\Authorize');
        try {
            \Zend\View\Helper\Navigation::setDefaultAcl($authorize->getAcl());
            \Zend\View\Helper\Navigation::setDefaultRole($authorize->getIdentity());
        } catch (\Doctrine\DBAL\DBALException $exception) {
            // database tables not yet initialized
        }

        $changeLanguageDetector = $serviceManager->get('ChangeLanguageDetector.listener');
        $eventManager->attachAggregate($changeLanguageDetector);
        
        $em = $e->getApplication()->getServiceManager()->get('Doctrine\ORM\EntityManager');
        $platform = $em->getConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('gender', 'string');
        $platform->registerDoctrineTypeMapping('car_status', 'string');
        $platform->registerDoctrineTypeMapping('cleanliness', 'string');
        $platform->registerDoctrineTypeMapping('_text', 'string');
        $platform->registerDoctrineTypeMapping('_int4', 'string');
        $platform->registerDoctrineTypeMapping('geometry', 'string');
        $platform->registerDoctrineTypeMapping('jsonb', 'string');
        $platform->registerDoctrineTypeMapping('reservations_archive_reason', 'string');
        $platform->registerDoctrineTypeMapping('invoice_type', 'string');
        $platform->registerDoctrineTypeMapping('trip_payment_status', 'string');
        $platform->registerDoctrineTypeMapping('polygon', 'string');
        $platform->registerDoctrineTypeMapping('extra_payments_types', 'string');
        $platform->registerDoctrineTypeMapping('disabled_reason', 'string');
        $platform->registerDoctrineTypeMapping('reactivation_reason', 'string');
        $platform->registerDoctrineTypeMapping('csv_anomaly_type', 'string');
        $platform->registerDoctrineTypeMapping('gender', 'string');
        $platform->registerDoctrineTypeMapping('car_status', 'string');
        $platform->registerDoctrineTypeMapping('cleanliness', 'string');
        $platform->registerDoctrineTypeMapping('_text', 'string');
        $platform->registerDoctrineTypeMapping('_int4', 'string');
        $platform->registerDoctrineTypeMapping('geometry', 'string');
        $platform->registerDoctrineTypeMapping('jsonb', 'string');
        $platform->registerDoctrineTypeMapping('reservations_archive_reason', 'string');
        $platform->registerDoctrineTypeMapping('invoice_type', 'string');
        $platform->registerDoctrineTypeMapping('trip_payment_status', 'string');
        $platform->registerDoctrineTypeMapping('polygon', 'string');
        $platform->registerDoctrineTypeMapping('extra_payments_types', 'string');
        $platform->registerDoctrineTypeMapping('disabled_reason', 'string');
        $platform->registerDoctrineTypeMapping('reactivation_reason', 'string');
        $platform->registerDoctrineTypeMapping('csv_anomaly_type', 'string');

    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    // View Helper Configuration
    public function getViewHelperConfig()
    {
        return [
            'factories' => [
                'languageMenuHelper' => 'Application\\View\\Helper\\LanguageMenuHelperFactory'
            ],
        ];
    }
}
