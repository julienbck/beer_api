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
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Security;
use Nelmio\ApiDocBundle\Annotation\Model;


/**
 * Class BreweryController
 * @package App\Controller\Rest
 *
 * @SWG\Tag(name="Breweries")
 * @Security(name="Bearer")
 */
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
     * @SWG\Response(
     *     response=200,
     *     description="Return collection of breweries",
     *     @Model(type=Brewery::class)
     * )
     * @SWG\Parameter(name="page", in="query", type="integer", description="Page to paginate result. Totalpage in response header")
     * @SWG\Parameter(name="limit", in="query", type="integer", description="limit elements by page")
     *
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
     * @SWG\Response(
     *     response=200,
     *     description="Return brewery",
     *     @Model(type=Brewery::class)
     * )
     *
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
     * @SWG\Response(
     *     response=201,
     *     description="Create brewery",
     * )
     *
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
     * @SWG\Response(
     *     response=204,
     *     description="Update brewery",
     * )
     *
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
     * @SWG\Response(
     *     response=204,
     *     description="Delete beer",
     * )
     *
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
     * @SWG\Response(
     *     response=200,
     *     description="Return number breweries by country",
     * )
     * @SWG\Parameter(name="sort", in="query", type="string", description="sort by ASC|DESC. Value need full uppercase")
     *
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