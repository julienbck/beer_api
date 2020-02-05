<?php


namespace App\Controller\Rest;


use App\DTO\Assembler\CheckinAssembler;
use App\DTO\CheckinDTO;
use App\Entity\Checkin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Common\QueryAnnotation;

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
     * @Route("/checkins", name="post_checkins", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function post(Request $request) :Response
    {
        $response = $this->postEntity($request->getContent(), CheckinDTO::class, $this->checkinAssembler);

        return $response;
    }

    /**
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

        $response = $this->patchEntity($request, CheckinDTO::class, $this->checkinAssembler, $checkin);

        return $response;
    }

    /**
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