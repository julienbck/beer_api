<?php


namespace App\DTO\Assembler;


use App\DTO\UserDTO;
use App\Entity\User;
use Doctrine\Migrations\Configuration\Exception\JsonNotValid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\SodiumPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAssembler
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * UserAssembler constructor.
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function hydrateEntity(UserDTO $userDTO)
    {
        $newUser = new User();

        $newUser
            ->setEmail($userDTO->getEmail())
            ->setUsername($userDTO->getUsername())
            ->setRoles(['ROLE_USER'])
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        $newUser->setPassword($this->passwordEncoder->encodePassword($newUser, $userDTO->getPassword()));

        return $newUser;
    }

    public function hydrateEntityPatch(UserDTO $userDTO, User $user): User
    {
        if (!is_null($userDTO->getEmail())) {
            if (!filter_var($userDTO->getEmail(), FILTER_VALIDATE_EMAIL)) {
                throw new JsonNotValid('Not good email format');
            }
            $user->setEmail($userDTO->getEmail());
        }

        if (!is_null($userDTO->getUsername())) {
            throw new JsonNotValid('Not possible edit username');
        }

        if (!is_null($userDTO->getAvatar())) {
            $user->setAvatar($userDTO->getAvatar());
        }

        $user->setUpdatedAt(new \DateTime());

        return $user;
    }
}