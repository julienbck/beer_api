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
     * @param array $context
     * @return array
     */
    public function getCollectionEntity(string $className, $requestQuery, array $context)
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

        return [
            'data' => $this->serialize(iterator_to_array($pagerfanta->getCurrentPageResults()), $context),
            'totalPage' => $pagerfanta->getNbPages(),
            'totalHits' => $pagerfanta->getNbResults(),
            'nextPage' => $pagerfanta->getNextPage()
            ];
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

    public function serialize($data, $context)
    {
        return $this->serializer->serialize($data, 'json', SerializationContext::create()->setGroups($context));
    }
}