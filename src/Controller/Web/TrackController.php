<?php

namespace App\Controller\Web;

use App\Entity\Track;
use App\Form\TrackType;
use App\Repository\TrackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/track')]
class TrackController extends AbstractController
{
    #[Route('/', name: 'app_web_track_index', methods: ['GET'])]
    public function index(TrackRepository $trackRepository): Response
    {
        return $this->render('web/track/index.html.twig', [
            'tracks' => $trackRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_web_track_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $track = new Track();
        $form = $this->createForm(TrackType::class, $track);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($track);
            $entityManager->flush();

            return $this->redirectToRoute('app_web_track_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('web/track/new.html.twig', [
            'track' => $track,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_web_track_show', methods: ['GET'])]
    public function show(Track $track): Response
    {
        return $this->render('web/track/show.html.twig', [
            'track' => $track,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_web_track_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Track $track, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TrackType::class, $track);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_web_track_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('web/track/edit.html.twig', [
            'track' => $track,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_web_track_delete', methods: ['POST'])]
    public function delete(Request $request, Track $track, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$track->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($track);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_web_track_index', [], Response::HTTP_SEE_OTHER);
    }
}
