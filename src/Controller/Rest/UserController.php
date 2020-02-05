<?php


namespace App\Controller\Rest;


use App\DTO\Assembler\UserAssembler;
use App\DTO\UserDTO;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractRestController
{
    /**
     * @var BeerAssembler
     */
    private $userAssembler;

    /**
     * BeerController constructor.
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param UserAssembler $userAssembler
     */
    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, UserAssembler $userAssembler)
    {
        parent::__construct($serializer, $validator);

        $this->userAssembler = $userAssembler;
    }

    /**
     * @Route("/user/{id}", name="get_user", methods={"GET"}, requirements={"id"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function getOne(Request $request): Response
    {
        $idRessource = $request->get('id') ? $request->get('id') : null;

        $user = $this->getOneEntity(User::class, $idRessource);

        if (empty($user)) {
            return new Response(sprintf('User not found with id: %d', $idRessource), 404);
        }
        $json = $this->serialize($user, ['user-details-public', 'checkin-details']);

        return new Response($json, 200, ['Content-type' => 'application/json']);
    }

    /**
     * @Route("/user/register", name="create_user", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function post(Request $request)
    {
        $response = $this->postEntity($request->getContent(), UserDTO::class, $this->userAssembler);

        return $response;
    }

    /**
     * @Route("/user/{id}", name="patch_user", methods={"PATCH"}, requirements={"id"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function patch(Request $request) :Response
    {
        $userId = $request->attributes->get('id');

        if ($userId != $this->getUser()->getId()) {
            throw new AccessDeniedException('Not allowed to edit other profil');
        }

        $response = $this->patchEntity($request, UserDTO::class, $this->userAssembler, $this->getUser());

        return $response;
    }
}