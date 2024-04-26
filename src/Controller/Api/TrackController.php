<?php

namespace App\Controller\Api;

use App\Entity\Track;
use App\Repository\TrackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Track")]
class TrackController extends AbstractController
{
    public function __construct(
        private TrackRepository $trackRepository,
        private EntityManagerInterface $em,
        private SerializerInterface $serializer
    )
    {
        // ...
    }

    #[Route('/api/tracks', name: 'app_tracks_genre', methods: ['GET'])]

    public function index(): JsonResponse
    {
        $tracks = $this->trackRepository->findAll();

        return $this->json([
            'tracks' => $tracks,
        ], 200, [], [
            'groups' => ['read']
        ]);
    }

    #[Route('/api/track/{id}', name: 'app_api_track_get',  methods: ['GET'])]
    public function get(?Track $track = null): JsonResponse
    {
        if(!$track)
        {
            return $this->json([
                'error' => 'Ressource does not exist',
            ], 404);
        }

        return $this->json($track, 200, [], [
            'groups' => ['read']
        ]);
    }

    #[Route('/api/tracks', name: 'app_api_track_add',  methods: ['POST'])]
    public function add(
        #[MapRequestPayload('json', ['groups' => ['create']])] Track $track
    ): JsonResponse
    {
        $this->em->persist($track);
        $this->em->flush();
        
        return $this->json($track, 200, [], [
            'groups' => ['read']
        ]);
    }

    #[Route('/api/track/{id}', name: 'app_api_track_update',  methods: ['PUT'])]
    public function update(Track $track, Request $request): JsonResponse
    { 
        
        $data = $request->getContent();
        $this->serializer->deserialize($data, Track::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $track,
            'groups' => ['update']
        ]);

        $this->em->flush();

        return $this->json($track, 200, [], [
            'groups' => ['read'],
        ]);
    }

    #[Route('/api/track/{id}', name: 'app_api_track_delete',  methods: ['DELETE'])]
    public function delete(Track $track, Request $request): JsonResponse
    {

        $this->em->remove($track);
        $this->em->flush();
        
        return $this->json([
            'message' => "Le track a bien été supprimée"
        ], 200, [], [
            'groups' => ['read'],
        ]);
    }

  
    
}
