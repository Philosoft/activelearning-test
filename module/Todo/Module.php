<?php


namespace Todo;


use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Model\JsonModel;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module implements ConfigProviderInterface, AutoloaderProviderInterface
{
    public function onBootstrap(MvcEvent $e) {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onDispatchError'], 0);
        $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, [$this, 'onRenderError'], 0);
    }

    public function onDispatchError(MvcEvent $e)
    {
        return $this->getJsonModelError($e);
    }

    public function onRenderError(MvcEvent $e)
    {
        return $this->getJsonModelError($e);
    }

    public function getJsonModelError(MvcEvent $e)
    {
        $error = $e->getError();
        if (!$error) {
            return false;
        }

        $exception = $e->getParam('exception');

        $model = new JsonModel([
            "error" => $exception instanceof \Exception ? $exception->getMessage() : $error
        ]);

        $e->setResult($model);

        return $model;
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getControllerConfig()
    {
        return [
            "factories" => [
                __NAMESPACE__ . "\\Controller\\Todo" => function ($sm) {
                    /** @var ServiceLocatorAwareInterface $sm */
                    $locator = $sm->getServiceLocator();
                    $entityManager = $locator->get("doctrine.entitymanager.orm_default");
                    $hydrator = new DoctrineObject($entityManager);

                    return new Controller\TodoController($entityManager, $hydrator);
                }
            ]
        ];
    }
}
