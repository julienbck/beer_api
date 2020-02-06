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
use App\Common\QueryAnnotation;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Security;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * Class UserController
 * @package App\Controller\Rest
 *
 * @SWG\Tag(name="User")
 * @Security(name="Bearer")
 */
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
     * @SWG\Response(
     *     response=200,
     *     description="Return collection of beers",
     *     @Model(type=User::class),
     * )
     *
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
     * @SWG\Response(
     *     response=201,
     *     description="Create user",
     * )
     * @SWG\Parameter(name="body", in="body", description="Elements to update user", type="json", required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="username", type="string", example="gerard"
     *          ),
     *          @SWG\Property(property="password", type="string", example="monpa55w0rd!"
     *          ),
     *          @SWG\Property(property="email", type="email", example="beer@checkin.fr"
     *          ),
     *          @SWG\Property(property="avatar", type="string", example="http://google.com/image.jpg"
     *          ),
     *       )
     *     )
     *
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
     * @SWG\Response(
     *     response=201,
     *     description="Update user",
     * )
     * @SWG\Parameter(name="body", in="body", description="Elements to update user", type="json", required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="email", type="email", example="beer@checkin.fr"
     *          ),
     *          @SWG\Property(property="avatar", type="string", example="http://google.com/image.jpg"
     *          ),
     *       )
     *     )
     *
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

    /**
     * @SWG\Response(
     *     response=201,
     *     description="Delete user",
     * )
     *
     * @Route("/user/{id}", name="delete_user", methods={"DELETE"}, requirements={"id"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function deleteOne(Request $request):Response
    {
        $idRessource = $request->get('id') ? $request->get('id') : null;

        if (empty($idRessource)) {
            new Response(null, 404);
        }

        if ($idRessource != $this->getUser()->getId()) {
            throw new AccessDeniedException('Not allowed to delete this profile because your are not owner');
        }

        $resp = $this->delete(User::class, $idRessource);

        return $resp;
    }
}