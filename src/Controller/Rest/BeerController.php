<?php


namespace App\Controller\Rest;


use App\Entity\Beer;
use App\Repository\BeerRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BeerController extends AbstractRestController
{
    protected $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        parent::__construct($serializer);
    }

    /**
     * @Route("/beers", name="get_beers", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function getCollection(Request $request): Response
    {
        $results = $this->getCollectionEntity(Beer::class, $request->query, ['beer-collection']);

        if (empty($results['data'])) {
            return new Response(null, 204);
        }

        $response = new Response($results['data'], 200);
        $response->headers->set('totalHits', $results['totalHits']);
        $response->headers->set('totalPage', $results['totalPage']);
        $response->headers->set('nextPage', $results['nextPage']);

        return $response;
    }

    /**
     * @Route("/beers/{id}", name="get_beer", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function getOne(Request $request): Response
    {
        $beer = $this->getOneEntity(Beer::class, $request->get('id'));
        $json = $this->serialize($beer, ['beer-details']);

        return new Response($json, 200);
    }

    /**
     * @Route("/beers", name="post_beer", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function post(Request $request) :JsonResponse
    {

    }

    /**
     * @Route("/beers/{id}", name="patch_beer", methods={"PATCH"})
     * @param Request $request
     * @return JsonResponse
     */
    public function patch(Request $request) :JsonResponse
    {

    }

    public function serializeJsonFormat($data, $context)
    {
        return $this->serializer->serialize($data, 'json', SerializationContext::create()->setGroups($context));
    }
}