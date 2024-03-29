<?php
namespace Application\Controller;

use Application\Controller\Plugin\TranslatorPlugin;
use Application\Form\GroupForm;
use Application\Form\GroupMinutesLimitForm;
use BusinessCore\Entity\Group;
use BusinessCore\Service\BusinessService;
use BusinessCore\Service\GroupService;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * @method TranslatorPlugin translatorPlugin()
 */
class GroupsController extends AbstractActionController
{
    /**
     * @var BusinessService
     */
    private $businessService;
    /**
     * @var GroupService
     */
    private $groupService;
    /**
     * @var GroupForm
     */
    private $groupForm;
    /**
     * @var GroupMinutesLimitForm
     */
    private $groupMinutesLimitForm;

    /**
     * GroupsController constructor.
     * @param BusinessService $businessService
     * @param GroupService $groupService
     * @param GroupForm $groupForm
     * @param GroupMinutesLimitForm $groupMinutesLimitForm
     */
    public function __construct(
        BusinessService $businessService,
        GroupService $groupService,
        GroupForm $groupForm,
        GroupMinutesLimitForm $groupMinutesLimitForm
    ) {
        $this->businessService = $businessService;
        $this->groupService = $groupService;
        $this->groupForm = $groupForm;
        $this->groupMinutesLimitForm = $groupMinutesLimitForm;
    }

    public function groupsAction()
    {
        return new ViewModel([
            'business' => $this->identity()->getBusiness()
        ]);
    }

    public function addAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            try {
                $business = $this->identity()->getBusiness();
                $this->groupService->createNewGroup($business, $data);
                $this->flashMessenger()->addSuccessMessage($this->translatorPlugin()->translate('Gruppo creato con successo'));
                return $this->redirect()->toRoute('groups');
            } catch (UniqueConstraintViolationException $e) {
                $this->flashMessenger()->addErrorMessage($this->translatorPlugin()->translate("Esiste già un gruppo con questo nome"));
                return $this->redirect()->toRoute('groups/add');
            }
        }
        return new ViewModel([
            'form' => $this->groupForm
        ]);
    }

    public function detailsAction()
    {
        $currentGroup = $this->getCurrentGroup();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $this->groupService->setGroupLimit($currentGroup, $data);
            $this->flashMessenger()->addSuccessMessage($this->translatorPlugin()->translate('Limite minuti del gruppo aggiornati'));
            return $this->redirect()->toRoute('groups/details', ['id' => $currentGroup->getId()]);
        }
        return new ViewModel([
            'group' => $currentGroup,
            'form' => $this->groupMinutesLimitForm
        ]);
    }

    public function addEmployeesToGroupAction()
    {
        $group = $this->getCurrentGroup();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $userIdsToAdd = $this->getUserIdsFromPostData($data);
            $nInsert = $this->groupService->addEmployeesToGroup($userIdsToAdd, $group);
            if ($nInsert == 0) {
                $this->flashMessenger()->addSuccessMessage($this->translatorPlugin()->translate('Nessun dipendente selezionato'));
                return $this->redirect()->toRoute('groups/details/add-employees', ['id' => $group->getId()]);
            } else if ($nInsert == 1) {
                $message = $this->translatorPlugin()->translate('1 dipendente aggiunto al gruppo %s');
                $message = sprintf($message, $group->getName());
            } else {
                $message =  $this->translatorPlugin()->translate('%s dipendenti aggiunti al gruppo %s');
                $message = sprintf($message, $nInsert, $group->getName());
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
        $this->flashMessenger()->addSuccessMessage($this->translatorPlugin()->translate('Dipendente eliminato dal gruppo'));
        return $this->redirect()->toRoute('groups/details', ['id' => $group->getId()]);
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
