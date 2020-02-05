<?php


namespace App\Controller\Rest;


use App\DTO\Assembler\BreweryAssembler;
use App\DTO\BreweryDTO;
use App\Entity\Brewery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Common\QueryAnnotation;

class BreweryController extends AbstractRestController
{
    /**
     * @var BreweryAssembler
     */
    private $breweryAssembler;

    /**
     * BreweryController constructor.
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param BreweryAssembler $breweryAssembler
     */
    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, BreweryAssembler $breweryAssembler)
    {
        parent::__construct($serializer, $validator);
        $this->breweryAssembler = $breweryAssembler;
    }

    /**
     * @Route("/breweries", name="get_breweries", methods={"GET"})
     * @param Request $request
     * @return Response
     * @QueryAnnotation(name="page", type="integer", requirements="(\d+)")
     * @QueryAnnotation(name="limit", type="integer", requirements="(\d{2})")
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
        $response->headers->set('Content-type', 'application/json');

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

        return new Response($json, 200, ['Content-type' => 'application/json']);
    }

    /**
     * @Route("/breweries", name="post_breweries", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function post(Request $request) :Response
    {
        $response = $this->postEntity($request->getContent(), BreweryDTO::class, $this->breweryAssembler);

        return $response;
    }


    /**
     * @Route("/breweries/{id}", name="patch_brewery", methods={"PATCH"}, requirements={"id"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function patch(Request $request) :Response
    {
        $breweryId = $request->attributes->get('id');

        $brewery = $this->getDoctrine()->getRepository(Brewery::class)->find($breweryId);
        if (empty($brewery)) {
                throw $this->createNotFoundException(
                    'No product found for id '. $breweryId
                );
        }

        $response = $this->patchEntity($request, BreweryDTO::class, $this->breweryAssembler, $brewery);

        return $response;
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

    /**
     * @Route("/breweries/country/counter", name="get_country_brewery", methods={"get"})
     * @param Request $request
     * @return Response
     * @QueryAnnotation(name="sort", type="string", requirements="ASC|DESC")
     */
    public function getBreweryCountry(Request $request) : Response
    {
        $result = $this->getDoctrine()->getRepository(Brewery::class)->getNumberBreweryByCountry($request->query->get('sort'));
        $json = $this->serialize($result);

        return new Response($json, 200,  ['Content-type' => 'application/json']);
    }
}