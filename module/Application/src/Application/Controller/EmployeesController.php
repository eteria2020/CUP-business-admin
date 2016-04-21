<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use BusinessCore\Entity\Business;
use BusinessCore\Entity\Webuser;
use BusinessCore\Service\BusinessService;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\ViewModel;
use ZfcUser\Exception\AuthenticationEventException;

class EmployeesController extends AbstractActionController
{
    /**
     * @var Translator
     */
    private $translator;
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
     * @param Translator $translator
     * @param BusinessService $businessService
     * @param AuthenticationService $authService
     */
    public function __construct(
        Translator $translator,
        BusinessService $businessService,
        AuthenticationService $authService
    ){
        $this->translator = $translator;
        $this->businessService = $businessService;
        $this->authService = $authService;
    }

    public function employeesAction()
    {
        return new ViewModel([
            'business' => $this->getCurrentBusiness()
        ]);
    }

    /**
     * @return Business
     */
    private function getCurrentBusiness()
    {
        $user = $this->authService->getIdentity();
        if ($user instanceof Webuser) {
            return $user->getBusiness();
        } else {
            throw new AuthenticationEventException("Errore di autenticazione");
        }
    }

    public function approveEmployeeAction()
    {
        $businessCode = $this->getCurrentBusiness()->getCode();
        $employeeId = $this->params()->fromRoute('id', 0);

        $this->businessService->approveEmployee($businessCode, $employeeId);
        $this->flashMessenger()->addSuccessMessage($this->translator->translate('Dipendente approvato'));

        return $this->redirect()->toRoute('employees');
    }

    public function removeEmployeeAction()
    {
        $businessCode = $this->getCurrentBusiness()->getCode();
        $employeeId = $this->params()->fromRoute('id', 0);

        $this->businessService->removeEmployee($businessCode, $employeeId);
        $this->flashMessenger()->addSuccessMessage($this->translator->translate('Dipendente eliminato con successo'));

        return $this->redirect()->toRoute('employees');
    }

    public function blockEmployeeAction()
    {
        $businessCode = $this->getCurrentBusiness()->getCode();
        $employeeId = $this->params()->fromRoute('id', 0);

        $this->businessService->blockEmployee($businessCode, $employeeId);
        $this->flashMessenger()->addSuccessMessage($this->translator->translate('Dipendente bloccato con successo'));

        return $this->redirect()->toRoute('employees');
    }

    public function unblockEmployeeAction()
    {
        $businessCode = $this->getCurrentBusiness()->getCode();
        $employeeId = $this->params()->fromRoute('id', 0);

        $this->businessService->approveEmployee($businessCode, $employeeId);
        $this->flashMessenger()->addSuccessMessage($this->translator->translate('Dipendente sbloccato con successo'));

        return $this->redirect()->toRoute('employees');
    }
}
