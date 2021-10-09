<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Conseil;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/* #[Route('/avis')] */
class AvisController extends AbstractController
{
    #[Route('/avis', name: 'avis_index', methods: ['GET'])]
    public function index(AvisRepository $avisRepository): Response
    {

        return $this->render('avis/index.html.twig', [
            'avis' => $avisRepository->findAll(),
        ]);
    }

    #[Route('/avis/new/{id}', name: 'avis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, $id, Conseil $conseil, Avis $avi): Response
    {
        $avi = new Avis();
        /* on récupère l'entité user */
        $user = $this->getUser();
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* on set l'user de l'annonce avec l'user récupèrer plus haut */
            $avi->setUser($user);
            $avi->setConseil($conseil);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($avi);
            $entityManager->flush();

            return $this->redirectToRoute('conseil_show', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('avis/new.html.twig', [
            'avi' => $avi,
            'form' => $form,
        ]);
    }

    #[Route('/avis/show/{id}', name: 'avis_show', methods: ['GET'])]
    public function show(Avis $avi): Response
    {
        return $this->render('avis/show.html.twig', [
            'avi' => $avi,
        ]);
    }

    #[Route('/avis/{id}/edit', name: 'avis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Avis $avi): Response
    {
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('avis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('avis/edit.html.twig', [
            'avi' => $avi,
            'form' => $form,
        ]);
    }

    #[Route('/avis/{id}/delete', name: 'avis_delete', methods: ['POST', 'GET'])]
    public function delete(Request $request, Avis $avi): Response
    {
        if ($this->isCsrfTokenValid('delete'.$avi->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($avi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('conseil_index', [], Response::HTTP_SEE_OTHER);
    }
}
