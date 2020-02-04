<?php


namespace App\Controller\Rest;


use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbstractRestController extends AbstractController
{

    protected $serializer;

    protected $validator;

    public function __construct(\Symfony\Component\Serializer\SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
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

    /**
     * @param $className
     * @param $idRessource
     * @return Response
     */
    public function delete($className, $idRessource)
    {
        $result = $this->getDoctrine()->getRepository($className)->find($idRessource);

        if (empty($result)) {
            return new Response('Not found', 404);
        }

        if ($result) {
            $this->getDoctrine()->getManager()->remove($result);
            $this->getDoctrine()->getManager()->flush();

            return new Response(null, 204);
        }
    }

    /**
     * @param $requestContent
     * @param $classNameDTO
     */
    public function postEntity($requestContent, $classNameDTO, $assemblerDTO): Response
    {
        try {
            $entityDTO = $this->deserialize($requestContent, $classNameDTO);
            $errors = $this->validator->validate($entityDTO);
            $errorsMessage = [];
            if (0 !== count($errors)) {
                foreach ($errors as $error) {
                    $errorsMessage[] = $error->getMessage().' Property:  '.$error->getPropertyPath();
                }
                return new JsonResponse($errorsMessage, 400);
            }

            $entityHydrated  = $assemblerDTO->hydrateentity($entityDTO);

            $this->getDoctrine()->getManager()->persist($entityHydrated);
            $this->getDoctrine()->getManager()->flush();
            $this->getDoctrine()->getManager()->clear();

            return new Response(null, 201);
        } catch(\Exception $e) {
            return new Response($e->getMessage(), 400);
        }
    }

    public function serialize($data, $context = null)
    {
        return $this->serializer->serialize($data, 'json', ['groups' => $context]);
    }

    public function deserialize($data, $className = null)
    {
        return $this->serializer->deserialize($data, $className,'json');
    }
}