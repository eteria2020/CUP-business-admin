<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use BusinessCore\Entity\Webuser;
use BusinessCore\Service\BusinessService;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZfcUser\Exception\AuthenticationEventException;

class EmployeesController extends AbstractActionController
{
    /**
     * @var BusinessService
     */
    private $businessService;
    /**
     * @var AuthenticationService
     */
    private $authService;

    /**
     * EmployeesController constructor.
     * @param BusinessService $businessService
     * @param AuthenticationService $authService
     */
    public function __construct(
        BusinessService $businessService,
        AuthenticationService $authService
    ) {
        $this->businessService = $businessService;
        $this->authService = $authService;
    }

    public function employeesAction()
    {
        return new ViewModel([
            'business' => $business = $this->retrieveAuthenticatedUser()->getBusiness()
        ]);
    }

    public function approveEmployeeAction()
    {
        $business = $this->retrieveAuthenticatedUser()->getBusiness();
        $employeeId = $this->params()->fromRoute('id', 0);

        $this->businessService->approveEmployee($business, $employeeId);
        $this->flashMessenger()->addSuccessMessage($this->translatorPlugin()->translate('Dipendente approvato'));

        return $this->redirect()->toRoute('employees');
    }

    public function removeEmployeeAction()
    {
        $business = $this->retrieveAuthenticatedUser()->getBusiness();
        $employeeId = $this->params()->fromRoute('id', 0);

        $this->businessService->removeEmployee($business, $employeeId);
        $this->flashMessenger()->addSuccessMessage($this->translatorPlugin()->translate('Dipendente eliminato con successo'));

        return $this->redirect()->toRoute('employees');
    }

    public function blockEmployeeAction()
    {
        $business = $this->retrieveAuthenticatedUser()->getBusiness();
        $employeeId = $this->params()->fromRoute('id', 0);

        $this->businessService->blockEmployee($business, $employeeId);
        $this->flashMessenger()->addSuccessMessage($this->translatorPlugin()->translate('Dipendente bloccato con successo'));

        return $this->redirect()->toRoute('employees');
    }

    public function unblockEmployeeAction()
    {
        $business = $this->retrieveAuthenticatedUser()->getBusiness();
        $employeeId = $this->params()->fromRoute('id', 0);

        $this->businessService->approveEmployee($business, $employeeId);
        $this->flashMessenger()->addSuccessMessage($this->translatorPlugin()->translate('Dipendente sbloccato con successo'));

        return $this->redirect()->toRoute('employees');
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
