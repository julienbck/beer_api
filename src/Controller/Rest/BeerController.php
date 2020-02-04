<?php


namespace App\Controller\Rest;


use App\Common\QueryAnnotationReader;
use App\Common\QueryParamValidator;
use App\DTO\Assembler\BeerAssembler;
use App\DTO\BeerDTO;
use App\Entity\Beer;
use App\Entity\Brewery;
use App\Form\BeerType;
use App\Repository\BeerRepository;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Common\QueryAnnotation;

class BeerController extends AbstractRestController
{
    /**
     * @var BeerAssembler
     */
    private $beerAssembler;
    /**
     * @var QueryParamValidator
     */
    private $queryParamValidator;

    /**
     * BeerController constructor.
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param BeerAssembler $beerAssembler
     */
    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, BeerAssembler $beerAssembler, QueryParamValidator $queryParamValidator)
    {
        parent::__construct($serializer, $validator);

        $this->beerAssembler = $beerAssembler;
        $this->queryParamValidator = $queryParamValidator;
    }

    /**
     * @Route("/beers", name="get_beers", methods={"GET"})
     * @param Request $request
     * @return Response
     * @QueryAnnotation(name="page", type="integer", requirements="(\d+)")
     * @QueryAnnotation(name="limit", type="integer", requirements="(\d{2})")
     */
    public function getCollection(Request $request): Response
    {
        $this->queryParamValidator->validate($request, $this, __METHOD__);

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
     * @param Request $request
     * @return Response
     */
    public function getOne(Request $request): Response
    {
        $this->queryParamValidator->validate($request, $this, __METHOD__);

        $idRessource = $request->get('id') ? $request->get('id') : null;

        $beer = $this->getOneEntity(Beer::class, $idRessource);

        if (empty($beer)) {
            return new Response(sprintf('Not beer found with id: %d', $idRessource), 404);
        }
        $json = $this->serialize($beer, ['beer-details']);

        return new Response($json, 200, ['Content-type' => 'application/json']);
    }

    /**
     * @Route("/beers", name="post_beer", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function post(Request $request) :Response
    {
            $response = $this->postEntity($request->getContent(), BeerDTO::class, $this->beerAssembler);

            return $response;
    }

    /**
     * @Route("/beers/{id}", name="patch_beer", methods={"PATCH"}, requirements={"id"="\d+"})
     * @param Request $request
     * @return JsonResponse
     */
    public function patch(Request $request) :JsonResponse
    {

    }

    /**
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
     * @Route("/beers/filter/max", name="get_beers_max_attribute", methods={"GET"})
     * @param Request $request
     * @return Response
     * @QueryAnnotation(name="attribute", type="string", requirements="ibu|abv")
     */
    public function getBeersByAbv(Request $request)
    {
        $this->queryParamValidator->validate($request, $this, __METHOD__);
        $beers = $this->getDoctrine()->getRepository(Beer::class)->getBeerByMaxAttribute($request->get('attribute'));
        $json = $this->serialize($beers, ['beer-details']);

        return new Response($json, 200, ['Content-type' => 'application/json']);
    }
}