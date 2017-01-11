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
        $this->view->setTerminal(true);
    }

    /**
     * @return void
     */
    public function indexAction()
    {
        $adminNavigation = $this->adminNavigationWidget();
        if (null !== $adminNavigation) {
            $this->view->addChild($adminNavigation, 'adminNavigation');
        }

        return $this->view;
    }

    /**
     * @return void
     */
    public function loginAction()
    {
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

        if (!$this->accountService->login($values['username'], $values['password'])) {

            $this->flashMessenger()->addErrorMessage('The credentials provided are incorrect.');
            return $this->view;
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
        // @todo response 401
        return $this->view;
    }

}
