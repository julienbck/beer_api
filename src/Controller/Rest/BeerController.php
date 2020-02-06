<?php


namespace App\Controller\Rest;

use App\DTO\Assembler\BeerAssembler;
use App\DTO\BeerDTO;
use App\Entity\Beer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Common\QueryAnnotation;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

/**
 * Class BeerController
 * @package App\Controller\Rest
 * @SWG\Tag(name="Beers")
 * @Security(name="Bearer")
 */
class BeerController extends AbstractRestController
{
    /**
     * @var BeerAssembler
     */
    private $beerAssembler;

    /**
     * BeerController constructor.
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param BeerAssembler $beerAssembler
     */
    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, BeerAssembler $beerAssembler)
    {
        parent::__construct($serializer, $validator);

        $this->beerAssembler = $beerAssembler;
    }


    /**
     * @SWG\Response(
     *     response=200,
     *     description="Return collection of beers",
     *     @Model(type=Beer::class)
     * )
     * @SWG\Parameter(name="page", in="query", type="integer", description="Page to paginate result. Totalpage in response header")
     * @SWG\Parameter(name="limit", in="query", type="integer", description="limit elements by page")
     * @SWG\Parameter(name="sort_desc_by_attribute",in="query", type="string", description="Sort desc beers results by attribute (abv or ibu)")
     * @SWG\Parameter(name="sort_asc_by_attribute",in="query", type="string", description="Sort asc beers results by attribute (abv or ibu)")
     *
     * @Route("/beers", name="get_beers", methods={"GET"})
     * @param Request $request
     * @return Response
     * @QueryAnnotation(name="page", type="integer", requirements="(\d+)")
     * @QueryAnnotation(name="limit", type="integer", requirements="(\d{2})")
     * @QueryAnnotation(name="sort_desc_by_attribute", type="string", requirements="abv|ibu")
     * @QueryAnnotation(name="sort_asc_by_attribute", type="string", requirements="abv|ibu")
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
        $response->headers->set('Content-type', 'application/json');

        return $response;
    }

    /**
     * @Route("/beers/{id}", name="get_beer", methods={"GET"}, requirements={"id"="\d+"})
     * @SWG\Response(
     *     response=200,
     *     description="Return specific beer",
     *     @Model(type=Beer::class)
     * )
     * @param Request $request
     * @return Response
     */
    public function getOne(Request $request): Response
    {
        $idRessource = $request->get('id') ? $request->get('id') : null;

        $beer = $this->getOneEntity(Beer::class, $idRessource);

        if (empty($beer)) {
            return new Response(sprintf('Not beer found with id: %d', $idRessource), 404);
        }
        $json = $this->serialize($beer, ['beer-details']);

        return new Response($json, 200, ['Content-type' => 'application/json']);
    }

    /**
     * @SWG\Response(
     *     response=201,
     *     description="Create beer",
     * )
     * @SWG\Parameter(name="body", in="body", description="Elements needed to create beer", type="json", required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="name", type="string", example="Jupiler"
     *          ),
     *          @SWG\Property(property="abv", type="integer", example="55"
     *          ),
     *          @SWG\Property(property="ibu", type="integer", example="34"
     *          ),
     *          @SWG\Property(property="brewery_id", type="integer", example="55 (not required)"
     *          ),
     *          @SWG\Property(property="style_id", type="integer", example="55 (not required)"
     *          ),
     *       )
     *     )
     *
     * @Route("/beers", name="post_beer", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function post(Request $request) :Response
    {
            $response = $this->postEntity($request->getContent(), BeerDTO::class, $this->beerAssembler);

            return $response;
    }

    /**
     * @SWG\Response(
     *     response=204,
     *     description="Update beer",
     * )
     * @SWG\Parameter(name="body", in="body", description="Elements to update beer", type="json", required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="name", type="string", example="Jupiler"
     *          ),
     *          @SWG\Property(property="abv", type="integer", example="55"
     *          ),
     *          @SWG\Property(property="ibu", type="integer", example="34"
     *          ),
     *          @SWG\Property(property="brewery_id", type="integer", example="55"
     *          ),
     *          @SWG\Property(property="style_id", type="integer", example="55"
     *          ),
     *       )
     *     )
     *
     * @Route("/beers/{id}", name="patch_beer", methods={"PATCH"}, requirements={"id"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function patch(Request $request) :Response
    {
        $beerId = $request->attributes->get('id');

        $beer = $this->getDoctrine()->getRepository(Beer::class)->find($beerId);
        if (empty($beer)) {
            throw $this->createNotFoundException(
                'No beer found for id '. $beerId
            );
        }

        $response = $this->patchEntity($request, BeerDTO::class, $this->beerAssembler, $beer);

        return $response;
    }

    /**
     * @SWG\Response(
     *     response=204,
     *     description="Delete beer",
     * )
     *
     * @Route("/beers/{id}", name="delete_beer", methods={"DELETE"}, requirements={"id"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function deleteOne(Request $request):Response
    {
        $idRessource = $request->get('id') ? $request->get('id') : null;

        if (empty($idRessource)) {
            new Response(null, 404);
        }

        $resp = $this->delete(Beer::class, $idRessource);

        return $resp;
    }

    /**
     * @SWG\Response(
     *     response=201,
     *     description="Return max beer in ranking by attribute",
     *     @Model(type=Beer::class)
     * )
     * @SWG\Parameter(
     *     name="attribute",
     *     in="query",
     *     type="string",
     *     required=true,
     *     description="abv (Alcohol by volum) or ibu (International Bitterness Unit)"
     * )
     *
     * @Route("/beers/ranking/max", name="get_beers_max_attribute", methods={"GET"})
     * @param Request $request
     * @return Response
     * @QueryAnnotation(name="attribute", type="string", requirements="ibu|abv", required=true)
     */
    public function getBeersByAbv(Request $request)
    {
        $beers = $this->getDoctrine()->getRepository(Beer::class)->getBeerByMaxAttribute($request->get('attribute'));
        $json = $this->serialize($beers, ['beer-details']);

        return new Response($json, 200, ['Content-type' => 'application/json']);
    }
}