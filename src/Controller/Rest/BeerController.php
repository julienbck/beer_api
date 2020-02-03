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
    protected $beerRepository;

    protected $serializer;

    public function __construct(BeerRepository $beerRepository, SerializerInterface $serializer)
    {
        $this->beerRepository = $beerRepository;

        $this->serializer = $serializer;
    }

    /**
     * @Route("/beers", name="get_beers", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function getCollection(Request $request): Response
    {
        $beers = $this->getCollectionEntity(Beer::class, $request->query, ['beer-collection']);

        if (empty($beers)) {
            return new Response(null, 204);
        }

        $json = $this->serializeJsonFormat($beers, ['beer-collection']);

        return new Response($json, 200);
    }

    /**
     * @Route("/beers/{id}", name="get_beer", methods={"GET"})
     * @param Request $request
     */
    public function getOne(Request $request)
    {
        $beer = $this->getOneEntity(Beer::class, $request->get('id'));
        $json = $this->serializer->serialize($beer, 'json');

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