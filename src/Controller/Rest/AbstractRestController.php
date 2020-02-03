<?php


namespace App\Controller\Rest;


use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AbstractRestController extends AbstractController
{

    protected $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param string $className
     * @param $requestQuery
     * @return string
     */
    public function getCollectionEntity(string $className, $requestQuery)
    {
        $page = 1;
        $limit = 10;
        if (!empty($requestQuery->get('page'))) {
            $page = (int) $requestQuery->get('page');
        }

        if (!empty($requestQuery->get('limit'))) {
            $limit = (int) $requestQuery->get('limit');
        }

        $qb =  $this->getDoctrine()->getRepository($className)->getCollection();
        $adapter = new DoctrineORMAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($limit);
        $pagerfanta->setCurrentPage($page);

        return iterator_to_array($pagerfanta->getCurrentPageResults());
    }

    public function getOneEntity($className, $id)
    {
       return $this->getDoctrine()->getRepository($className)->getOneById($id);
    }

    public function post(Request $request): JsonResponse
    {
        // TODO: Implement post() method.
    }

    public function patch(Request $request): JsonResponse
    {
        // TODO: Implement patch() method.
    }
}