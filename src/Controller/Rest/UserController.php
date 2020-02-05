<?php


namespace App\Controller\Rest;


use App\DTO\Assembler\UserAssembler;
use App\DTO\UserDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
     * @Route("/user/register", name="create_user", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function post(Request $request)
    {
        $response = $this->postEntity($request->getContent(), UserDTO::class, $this->userAssembler);

        return $response;
    }
}