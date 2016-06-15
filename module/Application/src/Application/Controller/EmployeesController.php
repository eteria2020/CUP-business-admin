<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Controller\Plugin\TranslatorPlugin;
use BusinessCore\Entity\Business;
use BusinessCore\Service\BusinessService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * @method TranslatorPlugin translatorPlugin()
 */
class EmployeesController extends AbstractActionController
{
    /**
     * @var BusinessService
     */
    private $businessService;

    /**
     * EmployeesController constructor.
     * @param BusinessService $businessService
     */
    public function __construct(BusinessService $businessService)
    {
        $this->businessService = $businessService;
    }

    public function employeesAction()
    {
        return new ViewModel([
            'business' => $business = $this->identity()->getBusiness()
        ]);
    }

    public function approveEmployeeAction()
    {
        /** @var Business $business */
        $business = $this->identity()->getBusiness();
        $employeeId = $this->params()->fromRoute('id', 0);
        if ($business->isEnabled()) {
            $this->businessService->approveEmployee($business, $employeeId);
            $this->flashMessenger()->addSuccessMessage($this->translatorPlugin()->translate('Dipendente approvato'));
        } else {
            $this->businessService->approveEmployeeWithBusinessNotEnabled($business, $employeeId);
            $this->flashMessenger()->addSuccessMessage($this->translatorPlugin()->translate("Dipendente approvato, in attesa primo pagamento dell'azienda"));
        }

        return $this->redirect()->toRoute('employees');
    }

    public function removeEmployeeAction()
    {
        $business = $this->identity()->getBusiness();
        $employeeId = $this->params()->fromRoute('id', 0);

        $this->businessService->removeEmployee($business, $employeeId);
        $this->flashMessenger()->addSuccessMessage($this->translatorPlugin()->translate('Dipendente eliminato con successo'));

        return $this->redirect()->toRoute('employees');
    }

    public function blockEmployeeAction()
    {
        $business = $this->identity()->getBusiness();
        $employeeId = $this->params()->fromRoute('id', 0);

        $this->businessService->blockEmployee($business, $employeeId);
        $this->flashMessenger()->addSuccessMessage($this->translatorPlugin()->translate('Dipendente bloccato con successo'));

        return $this->redirect()->toRoute('employees');
    }

    public function unblockEmployeeAction()
    {
        $business = $this->identity()->getBusiness();
        $employeeId = $this->params()->fromRoute('id', 0);

        $this->businessService->unblockEmployee($business, $employeeId);
        $this->flashMessenger()->addSuccessMessage($this->translatorPlugin()->translate('Dipendente sbloccato con successo'));

        return $this->redirect()->toRoute('employees');
    }
}
