<?php


namespace App\Controller\Rest;


use App\Entity\Brewery;
use App\Repository\BreweryRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BreweryController extends AbstractRestController
{

    protected $breweryRepository;

    protected $serializer;

    public function __construct(BreweryRepository $breweryRepository, SerializerInterface $serializer)
    {
        $this->breweryRepository = $breweryRepository;

        $this->serializer = $serializer;
    }


    /**
     * @Route("/breweries", name="get_breweries", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function getCollection(Request $request)
    {
        $breweries = $this->getCollectionEntity(Brewery::class, $request->query);

        if (empty($breweries)) {
            return new Response(null, 204);
        }

        $json = $this->serializeJsonFormat($breweries, ['brewery-collection']);

        return new Response($json, 200);
    }

    /**
     * @Route("/breweries/{id}", name="get_brewery", methods={"GET"})
     * @param Request $request
     */
    public function getOne(Request $request) : Response
    {
        $brewery = $this->getOneEntity(Brewery::class, $request->get('id'));
        $json = $this->serializer->serialize($brewery, 'json');

        return new Response($json, 200);
    }

    /**
     * @Route("/breweries", name="post_brewery", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function post(Request $request) :JsonResponse
    {

    }

    /**
     * @Route("/breweries/{id}", name="patch_brewery", methods={"PATCH"})
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