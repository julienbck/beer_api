<?php


namespace App\Controller\Rest;


use App\DTO\Assembler\CheckinAssembler;
use App\DTO\CheckinDTO;
use App\Entity\Checkin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Common\QueryAnnotation;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Security;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * Class CheckinController
 * @package App\Controller\Rest
 *
 * @SWG\Tag(name="Checkins")
 * @Security(name="Bearer")
 */
class CheckinController extends AbstractRestController
{
    /**
     * @var CheckinAssembler
     */
    private $checkinAssembler;

    /**
     * CheckinController constructor.
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param CheckinAssembler $checkinAssembler
     */
    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, CheckinAssembler $checkinAssembler)
    {
        parent::__construct($serializer, $validator);
        $this->checkinAssembler = $checkinAssembler;
    }

    /**
     * @SWG\Response(
     *     response=200,
     *     description="Return collection of checkins",
     *     @Model(type=Checkin::class)
     * )
     * @SWG\Parameter(name="page", in="query", type="integer", description="Page to paginate result. Totalpage in response header")
     * @SWG\Parameter(name="limit", in="query", type="integer", description="limit elements by page")
     *
     * @Route("/checkins", name="get_checkins", methods={"GET"})
     * @param Request $request
     * @return Response
     * @QueryAnnotation(name="page", type="integer", requirements="(\d+)")
     * @QueryAnnotation(name="limit", type="integer", requirements="(\d{2})")
     */
    public function getCollection(Request $request)
    {
        $results = $this->getCollectionEntity(Checkin::class, $request->query, ['checkin-collection']);

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
     *     description="Return checkin",
     *     @Model(type=Checkin::class)
     * )
     *
     * @Route("/checkins/{id}", name="get_checkin", methods={"GET"}, requirements={"id"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function getOne(Request $request): Response
    {
        $idRessource = $request->get('id') ? $request->get('id') : null;

        $beer = $this->getOneEntity(Checkin::class, $idRessource);

        if (empty($beer)) {
            return new Response(sprintf('Not checkin found with id: %d', $idRessource), 404);
        }
        $json = $this->serialize($beer, ['checkin-details', 'beer-collection']);

        return new Response($json, 200, ['Content-type' => 'application/json']);
    }

    /**
     * @SWG\Response(
     *     response=201,
     *     description="Create checkin",
     * )
     *
     * @Route("/checkins", name="post_checkins", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function post(Request $request) :Response
    {

        if (empty($this->getUser())) {
           throw new \ErrorException('Need an user connected');
        }

        $response = $this->postEntity($request->getContent(), CheckinDTO::class, $this->checkinAssembler);

        return $response;
    }

    /**
     * @SWG\Response(
     *     response=204,
     *     description="Update checkin",
     * )
     *
     * @Route("/checkins/{id}", name="patch_checkin", methods={"PATCH"}, requirements={"id"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function patch(Request $request) :Response
    {
        $checkinId = $request->attributes->get('id');

        $checkin = $this->getDoctrine()->getRepository(Checkin::class)->find($checkinId);
        if (empty($checkin)) {
            throw $this->createNotFoundException(
                'No checkin found for id '. $checkinId
            );
        }

        if ($checkin->getUser()->getId() != $this->getUser()->getId()) {
            throw new AccessDeniedException('Not allowed to update checkins other member');
        }

        $response = $this->patchEntity($request, CheckinDTO::class, $this->checkinAssembler, $checkin);

        return $response;
    }

    /**
     * @SWG\Response(
     *     response=204,
     *     description="Delete checkin",
     * )
     *
     * @Route("/checkins/{id}", name="delete_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function deleteOne(Request $request):Response
    {
        $idRessource = $request->get('id') ? $request->get('id') : null;

        if (empty($idRessource)) {
            new Response(null, 404);
        }

        $resp = $this->delete(Checkin::class, $idRessource);

        return $resp;
    }
}