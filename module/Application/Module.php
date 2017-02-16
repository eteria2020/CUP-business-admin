<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use BjyAuthorize\View\RedirectionStrategy;
use Doctrine\DBAL\DBALException;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Validator\AbstractValidator;
use Zend\View\Helper\Navigation;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $application = $e->getApplication();
        $eventManager = $application->getEventManager();
        $serviceManager = $application->getServiceManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

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
            Navigation::setDefaultAcl($authorize->getAcl());
            Navigation::setDefaultRole($authorize->getIdentity());
        } catch (DBALException $exception) {
            // database tables not yet initialized
        }

        $changeLanguageDetector = $serviceManager->get('ChangeLanguageDetector.listener');
        $eventManager->attachAggregate($changeLanguageDetector);
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
}
