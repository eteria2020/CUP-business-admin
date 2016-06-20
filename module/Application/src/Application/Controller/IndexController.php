<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use BusinessCore\Service\BusinessPaymentService;
use BusinessCore\Service\SubscriptionService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    /**
     * @var SubscriptionService
     */
    private $subscriptionService;
    /**
     * @var BusinessPaymentService
     */
    private $businessPaymentService;


    /**
     * IndexController constructor.
     * @param SubscriptionService $subscriptionService
     * @param BusinessPaymentService $businessPaymentService
     */
    public function __construct(
        SubscriptionService $subscriptionService,
        BusinessPaymentService $businessPaymentService
    ) {
        $this->subscriptionService = $subscriptionService;
        $this->businessPaymentService = $businessPaymentService;
    }

    public function indexAction()
    {
        $business = $this->identity()->getBusiness();

        if ($this->getRequest()->isPost()) {
            $subscriptionPayment = $this->businessPaymentService->getBusinessSubscriptionPayment($business);
            $this->subscriptionService->paySubscription($subscriptionPayment);
        }
        return new ViewModel([
            'business' => $business
        ]);
    }
}
