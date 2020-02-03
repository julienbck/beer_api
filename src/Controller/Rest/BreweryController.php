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
    public function __construct(SerializerInterface $serializer)
    {
        parent::__construct($serializer);
    }

    /**
     * @Route("/breweries", name="get_breweries", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function getCollection(Request $request)
    {
        $results = $this->getCollectionEntity(Brewery::class, $request->query, ['brewery-collection']);

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
     * @Route("/breweries/{id}", name="get_brewery", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function getOne(Request $request) : Response
    {
        $brewery = $this->getOneEntity(Brewery::class, $request->get('id'));
        $json = $this->serialize($brewery, ['brewery-collection']);

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

    /**
     * @Route("/breweries/{id}", name="delete_brewery", methods={"DELETE"}, requirements={"id"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function deleteOne(Request $request):Response
    {
        $idRessource = $request->get('id') ? $request->get('id') : null;

        if (empty($idRessource)) {
            new Response(null, 404);
        }

        $resp = $this->delete(Brewery::class, $idRessource);

        return $resp;
    }
}