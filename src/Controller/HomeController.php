<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommentRepository;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


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
            
        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'comments' => $commentRepository->findAll(),
        ]);
    }

}
