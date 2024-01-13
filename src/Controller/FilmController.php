<?php

namespace App\Controller;


use App\Service\FilmService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
// use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;


use App\Entity\Film;

class FilmController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // This route is for the index page of the FilmController
    #[Route('api/film/test', name: 'app_film', methods: ['GET'])]
    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns a welcome message",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Film::class, groups={"film"}))
     *     )
     * )
     * @OA\Tag(name="Film")
     */
    public function index(Request $request): JsonResponse
    {
        if ($request->getMethod() !== 'GET') {
            return $this->json(['message' => 'Method not allowed'], 405);
        }

        return $this->json(['message' => 'Welcome to your new controller!']);
    }

    // This route is for getting a list of all movies
    #[Route('api/film/list', name: 'app_film_listing', methods: ['GET'])]
    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of films",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Film::class, groups={"film", "category:read"}))
     *     )
     * )
     * @OA\Tag(name="Film")
     */
    public function list(SerializerInterface $serializer, Request $request, FilmService $filmService): Response
    {
        $films = $filmService->getAllFilms(
            $request->query->get('page', 1),
            $request->query->get('pageSize', 10)
        );

        return $this->apiResponse($serializer, $films, $request->getAcceptableContentTypes(), '200',
            ['film', "category:read"]);
    }

    // This route is for getting a specific movie by ID
    #[Route('api/film/{id}', name: 'get_film', methods: ['GET'])]
    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns a film by ID",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Film::class, groups={"film", "category:read"}))
     *     )
     * )
     * @OA\Tag(name="Film")
     */
    public function getFilm(int $id, SerializerInterface $serializer, Request $request): Response
    {
        $film = $this->entityManager->getRepository(Film::class)->find($id);

        if (!$film) {
            return $this->json(['message' => 'Film not found'], 404);
        }

        return $this->apiResponse($serializer, ['film' => $film], $request->getAcceptableContentTypes()[0], 200,
            ['film', 'category:read']);
    }

    // This route is for creating a new movie
    #[Route('api/film', name: 'create_film', methods: ['POST'])]
    /**
     * @OA\RequestBody(
     *    description="Données du film à créer",
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=Film::class, groups={"film"}))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Film créé avec succès",
     *     @OA\JsonContent(ref=@Model(type=Film::class, groups={"film"}))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Le champ 'nom' est manquant"
     * )
     * @OA\Response(
     *     response=409,
     *     description="Le film existe déjà"
     * )
     * @OA\Tag(name="Film")
     */
    public function createFilm(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        $filmData = json_decode($request->getContent(), true);

        // Check required fields
        $requiredFields = ['nom', 'dateDeParution'];
        foreach ($requiredFields as $field) {
            if (!isset($filmData[$field])) {
                return $this->json(['message' => "The field '$field' is missing"], 400);
            }
        }

        // Check date format
        $dateDeParution = \DateTime::createFromFormat('Y-m-d', $filmData['dateDeParution']);
        if (!$dateDeParution) {
            return $this->json(['message' => "Invalid date format for 'dateDeParution'. Use 'Y-m-d' format."], 400);
        }

        // Check if film already exists
        $dbFilm = $entityManager->getRepository(Film::class)->findBy(["nom" => $filmData['nom']]);
        if($dbFilm) {
            return $this->json(['message' => "The film '" . $filmData['nom'] . "' already exists!"], 409);
        }

        // Create new film
        $film = $serializer->deserialize($request->getContent(), Film::class, 'json');
        $film->setDateDeParution($dateDeParution);

        $entityManager->persist($film);
        $entityManager->flush();

        return $this->apiResponse($serializer, ['message' => 'Film created successfully', 'film' => $film], $request->getAcceptableContentTypes()[0], 201,
            ['film', 'category:read']);
    }

    // This route is for editing an existing movie by ID
    #[Route('api/film/{id}', name: 'update_film', methods: ['PUT'])]
    /**
     * @OA\RequestBody(
     *    description="Données du film à mettre à jour",
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=Film::class, groups={"film"}))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Film mis à jour avec succès",
     *     @OA\JsonContent(ref=@Model(type=Film::class, groups={"film"}))
     * )
     * @OA\Response(
     *     response=404,
     *     description="Film non trouvé"
     * )
     * @OA\Tag(name="Film")
     */
    public function updateFilm(int $id, Request $request, SerializerInterface $serializer): Response
    {
        $film = $this->entityManager->getRepository(Film::class)->find($id);
        if (!$film) {
            return $this->json(['message' => 'Film not found'], 404);
        }

        $filmData = json_decode($request->getContent(), true);

        // Check required fields
        $requiredFields = ['nom', 'dateDeParution'];
        foreach ($requiredFields as $field) {
            if (!isset($filmData[$field])) {
                return $this->json(['message' => "The field '$field' is missing"], 400);
            }
        }

        // Check date format
        $dateDeParution = \DateTime::createFromFormat('Y-m-d', $filmData['dateDeParution']);
        if (!$dateDeParution) {
            return $this->json(['message' => "Invalid date format for 'dateDeParution'. Use 'Y-m-d' format."], 400);
        }

        $film->setNom($filmData['nom']);
        $film->setDescription($filmData['description'] ?? null);
        $film->setDateDeParution($dateDeParution);
        $film->setNote($filmData['note'] ?? null);

        $this->entityManager->persist($film);
        $this->entityManager->flush();

        return $this->apiResponse($serializer, ['message' => 'Film updated successfully'] , $request->getAcceptableContentTypes()[0], 200,
            ['film', 'category:read']);
    }

    // This route is for deleting an existing movie by ID
    #[Route('api/film/{id}', name: 'delete_film', methods: ['DELETE'])]
    /**
     * @OA\Response(
     *     response=200,
     *     description="Film supprimé avec succès",
     *     @OA\JsonContent(ref=@Model(type=Film::class, groups={"film"}))
     * )
     * @OA\Response(
     *     response=404,
     *     description="Film non trouvé"
     * )
     * @OA\Tag(name="Film")
     */
    public function deleteFilm(int $id, SerializerInterface $serializer, Request $request): Response
    {
        $film = $this->entityManager->getRepository(Film::class)->find($id);

        if ($film) {
            $this->entityManager->remove($film);
            $this->entityManager->flush();
            $message = 'Film deleted successfully';
            $statusCode = 200;
        } else {
            $message = 'Film not found';
            $statusCode = 404;
        }

        return $this->apiResponse($serializer, ['message' => $message], $request->getAcceptableContentTypes()[0], $statusCode,
            ['film', 'category:read']);
    }

    // This route is for searching for a movie by title or description
    #[Route('api/film/search/{searchTerm}', name: 'search_film', methods: ['GET'])]
    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns a list of films matching the search term",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Film::class, groups={"film", "category:read"}))
     *     )
     * )
     * @OA\Tag(name="Film")
     */
    public function searchFilm(string $searchTerm, SerializerInterface $serializer, Request $request): Response
    {
        $films = $this->entityManager->getRepository(Film::class)->findByTitleOrDescription($searchTerm);

        if (!$films) {
            return $this->json(['message' => 'No film found'], 404);
        }

        return $this->apiResponse($serializer, ['films' => $films], $request->getAcceptableContentTypes()[0], 200,
            ['film', 'category:read']);
    }

    // This route is for upload a poster for a movie
    #[Route('api/film/{id}/upload', name: 'upload_film_poster', methods: ['POST'])]
    /**
     * @OA\RequestBody(
     *    description="Poster du film à mettre à jour",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *             @OA\Property(
     *                 property="poster",
     *                 description="Poster du film",
     *                 type="string",
     *                 format="binary"
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Poster mis à jour avec succès",
     *     @OA\JsonContent(ref=@Model(type=Film::class, groups={"film"}))
     * )
     * @OA\Response(
     *     response=404,
     *     description="Film non trouvé"
     * )
     * @OA\Tag(name="Film")
     */
    public function uploadFilmPoster(int $id, Request $request, SerializerInterface $serializer): Response {
        $film = $this->entityManager->getRepository(Film::class)->find($id);

        if (!$film) {
            return $this->json(['message' => 'Film not found'], 404);
        }

        $poster = $request->files->get('poster');

        if (!$poster) {
            return $this->json(['message' => 'Poster not found'], 404);
        }

        $originalFilename = pathinfo($poster->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = $originalFilename.'-'.uniqid().'.'.$poster->guessExtension();

        try {
            $poster->move($this->getParameter('posters_directory'), $newFilename);
        } catch (FileException $e) {
            return $this->json(['message' => 'Failed to upload file'], 500);
        }

        $film->setImage($newFilename);
        $this->entityManager->flush();

        return $this->apiResponse($serializer, ['film' => $film], $request->getAcceptableContentTypes()[0], 200,
            ['film', 'category:read']);
    }

    // this function is to return a response in JSON or XML format
    public function apiResponse(SerializerInterface $serializer, $data, $format, $statusCode, $groups = null): Response
    {
        $context = SerializationContext::create()->setGroups($groups);
        $contentType = $format == 'application/xml' ? 'application/xml' : 'application/json';
        $format = $contentType == 'application/xml' ? 'xml' : 'json';

        $responseContent = $serializer->serialize($data, $format, $context);
        $response = new Response($responseContent, $statusCode);
        $response->headers->set('Content-Type', $contentType);

        return $response;
    }
}
