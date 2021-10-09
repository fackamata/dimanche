<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Avis;
use App\Entity\Conseil;
use App\Form\ConseilType;
use App\Repository\ConseilRepository;
use App\Service\CounterService;
use App\Service\FileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/conseil', name: 'conseil_')]
class ConseilController extends AbstractController
{
    private $username = "";
    private $user = null;

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ConseilRepository $conseilRepository): Response
    {
        return $this->render('conseil/index.html.twig', [
            'conseils' => $conseilRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, FileService $fileService): Response
    {
        $conseil = new Conseil();
        /* on récupère l'entité user */
        $this->user = $this->getUser();
        $form = $this->createForm(ConseilType::class, $conseil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* on set l'user de l'annonce avec l'user récupèrer plus haut */
            $conseil->setUser($this->user );

            //getData retourne l'entitée Conseil
            /** @var Conseil $conseil */
            $conseil = $form->getData();

            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();

            $fileService->upload($file, $conseil, 'photo');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($conseil);
            $entityManager->flush();

            return $this->redirectToRoute('conseil_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('conseil/new.html.twig', [
            'conseil' => $conseil,
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'show', methods: ['GET'])]
    public function show(Conseil $conseil ,CounterService $counterService): Response
    {
        $this->user  = $this->getUser();
        $avi = $conseil->getAvis();
        if($this->user  != null){

            $role = $this->getUser()->getRoles();
        }

        if($this->user  === null || $this->user ->getUsername() != $conseil->getUser()->getUsername() 
        && in_array("ROLE_ADMIN", $role) != true){
            
            $nbView = $counterService->countView($conseil->getNombreVue());
            $conseil->setNombreVue($nbView);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($conseil);
            $entityManager->flush();
        }

        if ($this->user  != null) {
            $this->user  = $this->getUser()->getId();
            // on récupère l'username de la personne loguer
            $this->username = $this->getUser()->getUsername();
        }

        return $this->render('conseil/show.html.twig', [
            'conseil' => $conseil,
            'avis' => $avi,
            'username' => $this->username,
            'user' => $this->user 
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Conseil $conseil, FileService $fileService): Response
    {
        $form = $this->createForm(ConseilType::class, $conseil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Conseil $conseil */
            $conseil = $form->getData();

            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();

            if ($file) {
                $fileService->upload($file, $conseil, 'photo');
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('conseil_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('conseil/edit.html.twig', [
            'conseil' => $conseil,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Conseil $conseil, FileService $fileService): Response
    {
        if ($this->isCsrfTokenValid('delete'.$conseil->getId(), $request->request->get('_token'))) {
            //remove the image file
            $fileService->remove($conseil, 'photo');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($conseil);
            $entityManager->flush();
        }

        return $this->redirectToRoute('conseil_index', [], Response::HTTP_SEE_OTHER);
    }
}
