<?php


namespace App\Listener;


use App\Common\QueryParamValidator;
use App\Controller\Rest\AbstractRestController;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpKernel\Controller\ErrorController;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class QueryAnnotationListener
{

    /**
     * @var QueryParamValidator
     */
    private $queryParamValidator;
    /**
     * @var Reader
     */
    private $reader;


    public function __construct(QueryParamValidator $queryParamValidator, Reader $reader)
    {

        $this->queryParamValidator = $queryParamValidator;
        $this->reader = $reader;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();
        if(!$controller instanceof ErrorController) {
            $reflecClass = new \ReflectionClass($controller[0]);

            if ($reflecClass->isSubclassOf(AbstractRestController::class)) {
                $this->queryParamValidator->validate($event->getRequest(), $controller[0], $controller[1]);
            }
        }
    }
}