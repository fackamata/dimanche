<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Avis;
use App\Entity\Conseil;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Repository\AnnonceRepository;
use App\Repository\AvisRepository;
use App\Repository\ConseilRepository;
use App\Repository\UserRepository;
use App\Service\FileService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, FileService $fileService): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            //getData retourne l'entitée User
            /** @var User $user */
            $user = $form->getData();

            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();

            $fileService->upload($file, $user, 'photo');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);

        
    }

    #[Route('/register/{id}', name: 'app_compte')]
    public function compte(User $user, UserService $userService): Response
    {
        $nbAnnonce = $userService->countAnnonce($user);
        $nbConseil = $userService->countConseil($user);
        $nbAvis = $userService->countAvis($user);
        $nbMessage = $userService->countMessage($user);
        $msgNonLu = $userService->countMsgNonLu($user);
        $msgdeluserenvoyer = $user->getMessagesEnvoyes();
        // dump($msgdeluserenvoyer);
        $msgdeluserrecu = $user->getMessagesRecus();
        // dd($msgdeluserrecu);
        return $this->render('registration/compte.html.twig', [
            'user' => $user,
            'nbAnnonce' => $nbAnnonce,
            'nbConseil' => $nbConseil,
            'nbAvis' => $nbAvis,
            'nbMessage' => $nbMessage,
            'msgNonLu' => $msgNonLu,
        ]);
    }

    #[Route('/register/{id}/edit', name: 'app_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserPasswordEncoderInterface $passwordEncoder, User $user, FileService $fileService): Response
    {
        // $form = $this->createForm(UserType::class, $user);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $previousImage = $user->getPhoto();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );


             //getData retourne l'entitée User
            /** @var User $user */
            $user = $form->getData();

            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
            
            if ($file != null) {
                $fileService->upload($file, $user, 'photo');
               
            }
            $this->getDoctrine()->getManager()->flush();
            $newImage = $user->getPhoto();
           

            return $this->redirectToRoute('app_compte', ['id'=> $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('registration/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/register/annonce/{id}', name:'app_annonce')]
    public function annonce( UserService $userService) : Response
    {
        $userAnnonce = $userService->findAnnonceByUser( $this->getUser());

        return $this->render('annonce/index.html.twig', [
            'annonces' => $userAnnonce,
        ]);
    }
    #[Route('/register/conseil/{id}', name:'app_conseil')]
    public function conseil( UserService $userService) : Response
    {
        $userConseil = $userService->findConseilByUser( $this->getUser());
        return $this->render('conseil/index.html.twig', [
            'conseils' => $userConseil,
        ]);
    }
    #[Route('/register/avis/{id}', name:'app_avis')]
    public function avis( UserService $userService) : Response
    {
        $userAvis= $userService->findAvisByUser( $this->getUser());
        return $this->render('avis/index.html.twig', [
            'avis' => $userAvis,
        ]);
    }

    #[Route('/register/message/{id}', name:'app_message_recu')]
    public function messageRecu( UserService $userService) : Response
    {   
        $user = $this->getUser();
        // tous les messages envoyé à l'utilisateur
        $userMessagesRecu= $userService->findMessageByUser( $this->getUser());

        $envoyer = false;
        return $this->render('message/recu.html.twig', [
            'messages' => $userMessagesRecu,
            'user' => $user,
            'envoyer' => $envoyer
        ]);
    }
    
    #[Route('/register/message/envoye/{id}', name:'app_message_envoye')]
    public function messageEnvoye( UserService $userService) : Response
    {   
        $user = $this->getUser();
        // tous les messages que l'utilisateur à envoyé
        $userMessagesEnvoye = $userService->findMessageBySender($this->getUser());
        return $this->render('message/envoye.html.twig', [
            'messages' => $userMessagesEnvoye,
            'user' => $user,
            'envoyer' => true

        ]);
    }
}
