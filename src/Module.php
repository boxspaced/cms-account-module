<?php
namespace Boxspaced\CmsAccountModule;

use Boxspaced\CmsAccountModule\Service\AccountService;
use Boxspaced\CmsCoreModule\Listener\ForceHttpsListener;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;

class Module
{

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * @param MvcEvent $event
     * @return void
     */
    public function onBootstrap(MvcEvent $event)
    {
        $eventManager = $event->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();

        $sharedEventManager->attach(
            AbstractActionController::class,
            MvcEvent::EVENT_DISPATCH,
            [$this, 'authorization'],
            100
        );

        $sharedEventManager->attach(
            AbstractActionController::class,
            MvcEvent::EVENT_DISPATCH,
            new ForceHttpsListener(),
            100
        );
    }

    /**
     * @param MvcEvent $event
     * @return Response|null
     */
    public function authorization(MvcEvent $event)
    {
        $controller = $event->getTarget();

        $controllerName = $event->getRouteMatch()->getParam('controller', null);
        $actionName = $event->getRouteMatch()->getParam('action', null);

        $actionMethod = $controller::getMethodFromAction($actionName);

        if (!method_exists($controller, $actionMethod)) {
            return null;
        }

        $accountService = $event->getApplication()->getServiceManager()->get(AccountService::class);

        if (!$accountService->isAllowed($controllerName, $actionName)) {

            if (null !== $accountService->getIdentity()) {

                return $controller->redirect()->toRoute('account', [
                    'action' => 'access-denied'
                ]);

            } else {

                $uri = $event->getApplication()->getRequest()->getUri();
                $uri->setScheme(null);
                $uri->setHost(null);
                $uri->setPort(null);
                $uri->setUserInfo(null);

                return $controller->redirect()->toRoute('account', [
                    'action' => 'login'
                ], [
                    'query' => [
                        'redirect' => $uri->toString(),
                    ]
                ]);
            }
        }
    }

}
