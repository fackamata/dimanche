<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\AnnonceRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/message')]
class MessageController extends AbstractController
{
    #[Route('/', name: 'message_index', methods: ['GET'])]
    public function index(MessageRepository $messageRepository): Response
    {
        return $this->render('message/index.html.twig', [
            'messages' => $messageRepository->findAll(),
        ]);
    }

    #[Route('/new/{idAnnonce}/{idAnnonceUser}/{idMessageSender}', name: 'message_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AnnonceRepository $annonceRepository, UserRepository $userRepository, $idAnnonce,$idMessageSender, $idAnnonceUser): Response
    {
        $userSender = $this->getUser(); // on récupère l'utilisateur qui envoie le message
        $userDest = $userRepository->findById($idAnnonceUser); // on récupère l'utilisateur à qui le message est destiné
        $messageAnnonceId = $annonceRepository->findById($idAnnonce); // on récupère l'utilisateur à qui le message est destiné
        // dd($userSender);
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $message->setDestinataire($userDest[0]);
            $message->setSender($userSender);
            $message->setAnnonce($messageAnnonceId[0]);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('annonce_show', ['id' => $idAnnonce], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('message/new.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    #[Route('/new/reponse/{idAnnonce}/{idAnnonceUser}/{idMessageSender}', name: 'message_new_reponse', methods: ['GET', 'POST'])]
    public function newReponse(Request $request, AnnonceRepository $annonceRepository, UserRepository $userRepository, $idAnnonce,$idMessageSender, $idAnnonceUser): Response
    {

        // on récupère l'utilisateur qui envoie le message
        $userSender = $this->getUser(); 
        // on récupère l'utilisateur à qui le message est destiné, comme c'est une réponse,
        // le sender devient le destinataire
        $userDest = $userRepository->findById($idMessageSender); 
        // on récupère l'utilisateur à qui le message est destiné
        $messageAnnonceId = $annonceRepository->findById($idAnnonce); 
        // dump($userSender);
        // dump($userDest[0]);
        // dd($messageAnnonceId[0]);
        // dd($userSender);
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $message->setDestinataire($userDest[0]);
            $message->setSender($userSender);
            $message->setAnnonce($messageAnnonceId[0]);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('annonce_show', ['id' => $idAnnonce], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('message/new.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    /* #[Route('/{id}', name: 'message_show', methods: ['GET'])]
    public function show(Message $message): Response
    {
        return $this->render('message/show.html.twig', [
            'message' => $message,
        ]);
    } */

    #[Route('/{id}', name: 'message_show', methods: ['GET'])]
    public function show(Message $message): Response
    {
        $repondre = false;
        $user = $this->getUser();
        $destinataire = $message->getDestinataire();
        //  dd($message->getSender());
        if($user === $destinataire){
            $message->setLu(true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            $entityManager->flush();
            $repondre = true;
        }
      
        return $this->render('message/show.html.twig', [
            'message' => $message,
            'sender' =>$message->getSender(),
            'destinataire' => $message->getDestinataire(),
            'annonce' => $message->getAnnonce(),
            'repondre' => $repondre
        ]);
    }

    #[Route('/{id}/edit', name: 'message_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Message $message): Response
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('message/edit.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'message_delete', methods: ['POST'])]
    public function delete(Request $request, Message $message): Response
    {
        $user = $this->getUser()->getId();
        if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($message);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_message_envoye', ['id' => $user], Response::HTTP_SEE_OTHER);
    }
}
