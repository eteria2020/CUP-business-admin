<?php

namespace Application\Controller;

use BusinessCore\Entity\Webuser;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use Zend\View\Model\ViewModel;
use ZfcUser\Exception\AuthenticationEventException;

class RateController extends AbstractActionController
{
    /**
     * @var AuthenticationService
     */
    private $authService;

    /**
     * RateController constructor.
     * @param AuthenticationService $authService
     */
    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }

    public function rateAction()
    {
        $business = $this->retrieveAuthenticatedUser()->getBusiness();
        $businessRate = $business->getBusinessRate();
        return new ViewModel([
            'business' => $business,
            'rate' => $businessRate
        ]);
    }

    /**
     * @return Webuser
     */
    private function retrieveAuthenticatedUser()
    {
        $user = $this->authService->getIdentity();
        if ($user instanceof Webuser) {
            return $user;
        } else {
            throw new AuthenticationEventException($this->translatorPlugin()->translate("Errore di autenticazione"));
        }
    }
}
