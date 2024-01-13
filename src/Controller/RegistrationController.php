<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class RegistrationController
{
    private $entityManager;
    private $passwordEncoder;
    private $validator;
    private $JWTManager;


    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder, ValidatorInterface $validator, JWTTokenManagerInterface $jwtManager)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->validator = $validator;
        $this->JWTManager = $jwtManager;
    }

    /**
     * @Route("/api/register", name="register", methods={"POST"})
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Registration"},
     *     summary="Register a new user",
     *     description="Register a new user",
     *     operationId="register",
     *     @OA\RequestBody(
     *         description="User to register",
     *         required=true,
     *         @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"user"}))
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     *   )
     * )
     */
    public function register(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Check if a user with the same email already exists
            $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
            if ($existingUser) {
                return new Response('A user with this email already exists', 400);
            }

            $user = new User();
            $user->setEmail($data['email']);
            $user->setRoles(['ROLE_USER']);

            $user->setPassword($this->passwordEncoder->hashPassword($user, $data['password'])); // Correct method

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return new Response('User registered successfully', 201);
        } catch (\Exception $e) {
            return new Response('An error occurred: ' . $e->getMessage(), 500);
        }
    }

    // Show all users
    /**
     * @Route("/api/users", name="users", methods={"GET"})
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Get all users",
     *     description="Get all users",
     *     operationId="users",
     *     @OA\Response(
     *         response=200,
     *         description="Users retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     *   )
     * )
     */
    public function users(Request $request): Response
    {
        try {
            $users = $this->entityManager->getRepository(User::class)->findAll();
            dd ($users);
            return new Response(json_encode($users), 200, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            return new Response('An error occurred: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @Route("/api/login", name="get_login", methods={"POST"})
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Login"},
     *     summary="Log in a user",
     *     description="Log in a user",
     *     operationId="login",
     *     @OA\RequestBody(
     *         description="User credentials",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function login(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if (!$user || !$this->passwordEncoder->isPasswordValid($user, $data['password'])) {
            return new Response('Invalid credentials', 400);
        }

        $token = $this->JWTManager->create($user);

        return new Response(json_encode(['token' => $token]), 200, ['Content-Type' => 'application/json']);
    }
}