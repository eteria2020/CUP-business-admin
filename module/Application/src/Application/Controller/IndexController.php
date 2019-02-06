<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\I18n\Translator;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


use BusinessCore\Entity\Business;
use BusinessCore\Service\BusinessService;
use BusinessCore\Form\InputData\BusinessDetails;
use BusinessCore\Form\Validator\RecipientCode;
use BusinessCore\Form\Validator\VatNumber;

use BusinessCore\Exception\InvalidBusinessFormException;

class IndexController extends AbstractActionController
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
     * IndexController constructor.
     * @param Translator $translator
     * @param BusinessService $businessService
     */
    public function __construct(Translator $translator, BusinessService $businessService)
    {
        $this->translator = $translator;
        $this->businessService = $businessService;

    }

    public function indexAction()
    {
        $business = $this->identity()->getBusiness();

        if ($this->getRequest()->isPost())
        {
            $this->businessEdit($business);
        }

        return new ViewModel([
            'business' => $business
        ]);
    }

    private function businessEdit(Business $business) {

        try {
            $received = $this->getRequest()->getPost();

            $vatNumber = $business->getVatNumber();
            $recipientCode = $business->getRecipientCode();
            $cem = $business->getCem();

            if(isset($received['vatNumber'])) {
                $vatNumber = strtoupper($received['vatNumber']);
                $vatNumberValidator = new VatNumber();
                if($vatNumberValidator->isValid($vatNumber)) {

                } else {
                    throw new InvalidBusinessFormException($this->translator->translate('Partita IVA errata'));
                }
            }

            if(isset($received['recipientCodeType'])) {
                if($received['recipientCodeType']=="recipientCode") {
                    $recipientCode = strtoupper($received['recipientCodeValue']);
                    $cem = null;

                    $recipientCodeValidator = new RecipientCode();
                    if($recipientCodeValidator->isValid($recipientCode)) {

                    } else {
                        throw new InvalidBusinessFormException($this->translator->translate('Codice destinatatio errato'));
                    }
                } else if($received['recipientCodeType']=="cem") {
                    $recipientCode = null;
                    $cem = strtolower($received['recipientCodeValue']);

                    $emailValidator = new \Zend\Validator\EmailAddress();
                    if($emailValidator->isValid($cem)) {

                    } else {
                        throw new InvalidBusinessFormException($this->translator->translate('PEC errata'));
                    }
                }
            }

            $businessDetails = new BusinessDetails(
                $business->getName(),
                $business->getDomains(),
                $business->getAddress(),
                $business->getZipCode(),
                $business->getProvince(),
                $business->getCity(),
                $vatNumber,
                $business->getEmail(),
                $business->getPhone(),
                $business->getFax(),
                $recipientCode,
                $cem);

            $this->businessService->updateBusinessDetails($business, $businessDetails);
            $this->flashMessenger()->addSuccessMessage($this->translator->translate('Azienda modificata con successo'));
        } catch (InvalidBusinessFormException $e) {
            $this->flashMessenger()->addErrorMessage($e->getMessage());
        }

    }
}
