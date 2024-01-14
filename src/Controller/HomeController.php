<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommentRepository;
use App\Entity\Comment;
use App\Entity\Contact;
use App\Form\CommentType;
use App\Form\ContactType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, CommentRepository $commentRepository, EntityManagerInterface $entityManager)
    {
        $comment = new Comment();

        $user = $this->getUser();

        $comment->setUser($user);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Votre commentaire a été envoyé'
            );
            return $this->redirectToRoute('app_home');
        }
            
        return $this->render('pages/index.html.twig', [
            'form' => $form->createView(),
            'comments' => $commentRepository->findAll(),
        ]);
    }

    #[Route('/service', name: 'app_service')]
    public function service() {

        return $this->render('pages/service.html.twig');
    }

    #[Route('/blog', name: 'app_blog')]
    public function blog() {

        return $this->render('pages/blog.html.twig');
    }

    #[Route('/coach', name: 'app_coach')]
    public function coach(UserRepository $userRepository) {
        $coachUsers = $userRepository->findByRole('ROLE_COACH');

        return $this->render('pages/coach.html.twig',[
            'users' => $coachUsers,

        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about( CommentRepository $commentRepository) {

        return $this->render('pages/about.html.twig',[
            'comments' => $commentRepository->findAll(),
        ]); 
        
    }



}
