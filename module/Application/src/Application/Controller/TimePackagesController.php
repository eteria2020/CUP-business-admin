<?php
namespace Application\Controller;

use BusinessCore\Entity\Webuser;
use BusinessCore\Service\BusinessTimePackageService;

use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZfcUser\Exception\AuthenticationEventException;

class TimePackagesController extends AbstractActionController
{
    /**
     * @var AuthenticationService
     */
    private $authService;
    /**
     * @var BusinessTimePackageService
     */
    private $businessTimePackageService;

    /**
     * EmployeesController constructor.
     * @param BusinessTimePackageService $businessTimePackageService
     * @param AuthenticationService $authService
     */
    public function __construct(
        BusinessTimePackageService $businessTimePackageService,
        AuthenticationService $authService
    ) {
        $this->businessTimePackageService = $businessTimePackageService;
        $this->authService = $authService;
    }

    public function timePackagesAction()
    {
        return new ViewModel([
            'business' => $this->retrieveAuthenticatedUser()->getBusiness()
        ]);
    }
    public function buyAction()
    {
        $timePackageId = $this->params()->fromRoute('id', 0);
        if ($timePackageId != 0) {
            $business = $this->retrieveAuthenticatedUser()->getBusiness();
            $this->businessTimePackageService->buyTimePackage($business, $timePackageId);
            $this->flashMessenger()->addSuccessMessage($this->translatorPlugin()->translate("Pacchetto minuti acquistato con sucesso"));

            return $this->redirect()->toRoute('time-packages');
        }

        return new ViewModel([
            'business' => $this->retrieveAuthenticatedUser()->getBusiness(),
            'packages' => $this->businessTimePackageService->getBuyablePackages()
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
