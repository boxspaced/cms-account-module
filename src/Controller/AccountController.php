<?php
namespace Boxspaced\CmsAccountModule\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Boxspaced\CmsAccountModule\Service;
use Zend\Log\Logger;
use Boxspaced\CmsAccountModule\Form;
use Zend\Uri\UriFactory;

class AccountController extends AbstractActionController
{

    /**
     * @var Service\AccountService
     */
    protected $accountService;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var ViewModel
     */
    protected $view;

    /**
     * @param Service\AccountService $accountService
     * @param Logger $logger
     */
    public function __construct(
        Service\AccountService $accountService,
        Logger $logger
    )
    {
        $this->accountService = $accountService;
        $this->logger = $logger;

        $this->view = new ViewModel();
    }

    /**
     * @return void
     */
    public function indexAction()
    {
        $this->layout('layout/admin');
        return $this->view;
    }

    /**
     * @return void
     */
    public function loginAction()
    {
        $this->layout('layout/dialog');

        $form = new Form\AccountLoginForm();
        $form->get('redirect')->setValue($this->params()->fromQuery('redirect'));

        $this->view->form = $form;

        if (!$this->getRequest()->isPost()) {
            return $this->view;
        }

        $form->setData($this->params()->fromPost());

        if (!$form->isValid()) {

            $this->flashMessenger()->addErrorMessage('Validation failed.');
            return $this->view;
        }

        $values = $form->getData();
        
        $identity = $this->accountService->login($values['username'], $values['password']);

        if (!$identity) {

            $this->flashMessenger()->addErrorMessage('The credentials provided are incorrect.');
            return $this->view;
        }
        
        if ($identity->changePassword) {
            
            return $this->redirect()->toRoute('account', [
                'action' => 'change-password',
            ], [
                'query' => [
                    'redirect' => $values['redirect'],
                ]
            ]);
        }

        if ($values['redirect']) {

            $uri = UriFactory::factory($values['redirect']);
            $uri->setScheme(null);
            $uri->setHost(null);
            $uri->setPort(null);
            $uri->setUserInfo(null);

            return $this->redirect()->toUrl($uri->toString());
        }

        return $this->redirect()->toRoute('account');
    }

    /**
     * @return void
     */
    public function changePasswordAction()
    {
        $this->layout('layout/dialog');

        $form = new Form\ChangePasswordForm();
        $form->get('redirect')->setValue($this->params()->fromQuery('redirect'));

        $this->view->form = $form;

        if (!$this->getRequest()->isPost()) {
            return $this->view;
        }

        $form->setData($this->params()->fromPost());

        if (!$form->isValid()) {

            $this->flashMessenger()->addErrorMessage('Validation failed.');
            return $this->view;
        }

        $values = $form->getData();

        $this->accountService->changePassword($values['password']);

        if ($values['redirect']) {

            $uri = UriFactory::factory($values['redirect']);
            $uri->setScheme(null);
            $uri->setHost(null);
            $uri->setPort(null);
            $uri->setUserInfo(null);

            return $this->redirect()->toUrl($uri->toString());
        }

        return $this->redirect()->toRoute('account');
    }

    /**
     * @return void
     */
    public function logoutAction()
    {
        $this->accountService->logout();

        $this->flashMessenger()->addSuccessMessage('Logout successful.');

        return $this->redirect()->toRoute('account', [
            'action' => 'login',
        ]);
    }

    /**
     * @return void
     */
    public function accessDeniedAction()
    {
        $this->view->setTerminal(true);
        return $this->view;
    }

}
