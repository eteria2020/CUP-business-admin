<?php
namespace Application\Controller;

use Application\Form\GroupForm;
use BusinessCore\Entity\Business;
use BusinessCore\Entity\Group;
use BusinessCore\Entity\Webuser;
use BusinessCore\Exception\InvalidGroupFormException;
use BusinessCore\Service\BusinessService;
use BusinessCore\Service\GroupService;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\ViewModel;
use ZfcUser\Exception\AuthenticationEventException;

class GroupsController extends AbstractActionController
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
     * @var GroupService
     */
    private $groupService;
    /**
     * @var GroupForm
     */
    private $groupForm;

    /**
     * GroupsController constructor.
     * @param Translator $translator
     * @param BusinessService $businessService
     * @param GroupService $groupService
     * @param AuthenticationService $authService
     * @param GroupForm $groupForm
     */
    public function __construct(
        Translator $translator,
        BusinessService $businessService,
        GroupService $groupService,
        AuthenticationService $authService,
        GroupForm $groupForm
    ) {
        $this->translator = $translator;
        $this->businessService = $businessService;
        $this->groupService = $groupService;
        $this->authService = $authService;
        $this->groupForm = $groupForm;
    }

    public function groupsAction()
    {
        return new ViewModel([
            'business' => $this->getCurrentBusiness()
        ]);
    }

    public function addAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost()->toArray();

            try {
                $this->groupService->createNewGroup($this->getCurrentBusiness(), $data);
                $this->flashMessenger()->addSuccessMessage($this->translator->translate('Gruppo creato con successo'));
                return $this->redirect()->toRoute('groups');
            } catch (InvalidGroupFormException $e) {
                $this->flashMessenger()->addSuccessMessage($e->getMessage());
                return $this->redirect()->toRoute('groups/add');
            }
        }
        return new ViewModel([
            'form' => $this->groupForm
        ]);
    }

    public function detailsAction()
    {
        return new ViewModel([
            'group' => $this->getCurrentGroup()
        ]);
    }

    public function addEmployeesToGroupAction()
    {
        $group = $this->getCurrentGroup();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost()->toArray();

            $nInsert = $this->groupService->addEmployeesToGroup($data, $group);
            if ($nInsert == 0) {
                $this->flashMessenger()->addSuccessMessage($this->translator->translate('Nessun dipendente selezionato'));
                return $this->redirect()->toRoute('groups/details/add-employees', ['id' => $group->getId()]);
            } else if ($nInsert == 1) {
                $message = $this->translator->translate('1 dipendente aggiunto al gruppo ' . $group->getName());
            } else {
                $message = $this->translator->translate($nInsert .' dipendenti aggiunti al gruppo ' . $group->getName());
            }
            $this->flashMessenger()->addSuccessMessage($message);

            return $this->redirect()->toRoute('groups/details', ['id' => $group->getId()]);
        }
        return new ViewModel([
            'group' => $group
        ]);
    }

    public function removeEmployeeFromGroupAction()
    {
        $group = $this->getCurrentGroup();
        $employeeId = $this->params()->fromRoute('employee', 0);
        $this->groupService->removeEmployeeFromGroup($group, $employeeId);
        $this->flashMessenger()->addSuccessMessage($this->translator->translate('Cliente eliminato dal gruppo'));
        return $this->redirect()->toRoute('groups/details', ['id' => $group->getId()]);
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

    /**
     * @return null|Group
     */
    private function getCurrentGroup()
    {
        $groupId = $this->params()->fromRoute('id', 0);
        return $this->groupService->getGroupById($groupId);
    }
}