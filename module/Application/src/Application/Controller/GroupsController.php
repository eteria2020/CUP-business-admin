<?php
namespace Application\Controller;

use Application\Form\GroupForm;
use BusinessCore\Entity\Business;
use BusinessCore\Entity\Group;
use BusinessCore\Entity\Webuser;
use BusinessCore\Service\BusinessService;
use BusinessCore\Service\GroupService;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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
            } catch (UniqueConstraintViolationException $e) {
                $this->flashMessenger()->addErrorMessage($this->translator->translate("Esiste già un gruppo con questo nome"));
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
            $userIdsToAdd = $this->getUserIdsFromPostData($data);
            $nInsert = $this->groupService->addEmployeesToGroup($userIdsToAdd, $group);
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
            throw new AuthenticationEventException($this->translator->translate("Errore di autenticazione"));
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

    /**
     * This function receive raw $data from the form post
     * selected users come in $data as 'add-1234' => 'on' where 1234 is the id of the user
     * @param $data
     * @return array
     */
    private function getUserIdsFromPostData($data)
    {
        $userIds = [];
        foreach ($data as $key => $value) {
            if (substr($key, 0, 3) === 'add' && $value === 'on') {
                $employeeId = substr($key, 4);
                $userIds[] = $employeeId;
            }
        }
        return $userIds;
    }
}