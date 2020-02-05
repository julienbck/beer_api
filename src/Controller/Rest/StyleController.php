<?php


namespace App\Controller\Rest;


use App\DTO\Assembler\StyleAssembler;
use App\DTO\StyleDTO;
use App\Entity\Style;
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
 * Class StyleController
 * @package App\Controller\Rest
 *
 * @SWG\Tag(name="Styles")
 * @Security(name="Bearer")
 */
class StyleController extends AbstractRestController
{

    /**
     * @var StyleAssembler
     */
    private $styleAssembler;

    /**
     * StyleController constructor.
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param StyleAssembler $styleAssembler
     * @QueryAnnotation(name="page", type="integer", requirements="(\d+)")
     * @QueryAnnotation(name="limit", type="integer", requirements="(\d{2})")
     */
    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, StyleAssembler $styleAssembler)
    {
        parent::__construct($serializer, $validator);
        $this->styleAssembler = $styleAssembler;
    }

    /**
     * @SWG\Response(
     *     response=200,
     *     description="Return collection of styles",
     *     @Model(type=Style::class)
     * )
     * @SWG\Parameter(name="page", in="query", type="integer", description="Page to paginate result. Totalpage in response header")
     * @SWG\Parameter(name="limit", in="query", type="integer", description="limit elements by page")
     *
     * @Route("/styles", name="get_styles", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function getCollection(Request $request): Response
    {
        $results = $this->getCollectionEntity(Style::class, $request->query, ['style-collection']);

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
     *     description="Return style",
     *     @Model(type=Style::class)
     * )
     *
     * @Route("/styles/{id}", name="get_style", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function getOne(Request $request) :Response
    {
        $style =  $this->getOneEntity(Style::class, $request->get('id'));
        $json = $this->serialize($style, ['style-collection']);

        return new Response($json, 200, ['Content-type' => 'application/json']);
    }

    /**
     * @SWG\Response(
     *     response=201,
     *     description="Create style",
     * )
     *
     * @Route("/styles", name="post_styles", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function post(Request $request) :Response
    {
        $response = $this->postEntity($request->getContent(), StyleDTO::class, $this->styleAssembler);

        return $response;
    }

    /**
     * @SWG\Response(
     *     response=204,
     *     description="Update style",
     * )
     *
     * @Route("/styles/{id}", name="patch_styles", methods={"PATCH"})
     * @param Request $request
     * @return Response
     */
    public function patch(Request $request) :Response
    {
        $styleId = $request->attributes->get('id');

        $style = $this->getDoctrine()->getRepository(Style::class)->find($styleId);
        if (empty($style)) {
            throw $this->createNotFoundException(
                'No style found for id '. $styleId
            );
        }

        $response = $this->patchEntity($request, StyleDTO::class, $this->styleAssembler, $style);

        return $response;
    }

    /**
     * @SWG\Response(
     *     response=201,
     *     description="Delete style",
     * )
     *
     * @Route("/styles/{id}", name="delete_style", methods={"DELETE"}, requirements={"id"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function deleteOne(Request $request):Response
    {
        $idRessource = $request->get('id') ? $request->get('id') : null;

        if (empty($idRessource)) {
            new Response(null, 404);
        }

        $resp = $this->delete(Style::class, $idRessource);

        return $resp;
    }

    /**
     * @SWG\Response(
     *     response=200,
     *     description="Return number of beers by style",
     * )
     *
     * @Route("/styles/beers/counter", name="get_styles_beers_quantity", methods={"GET"})
     * @param Request $request
     * @return Response
     * @QueryAnnotation(name="sort", type="string", requirements="ASC|DESC")
     * @QueryAnnotation(name="style_id", type="integer", requirements="(\d+)")
     */
    public function getStyleByBeersQuantity(Request $request)
    {
        $result = $this->getDoctrine()->getRepository(Style::class)->getNumberBeersByStyle($request->query->get('sort'), $request->query->get('style_id'));
        $json = $this->serialize($result);

        return new Response($json, 200,  ['Content-type' => 'application/json']);
    }
}