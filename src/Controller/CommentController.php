<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comment')]
class CommentController extends AbstractController
{
    #[Route('/', name: 'app_comment_index')]
    public function index(CommentRepository $commentRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied. You need to have the ADMIN role.');
        }
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_comment_show')]
    public function show(Comment $comment): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied. You need to have the ADMIN role.');
        }
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/delete/{id}', name: 'comment_delete')]
    public function delete(EntityManagerInterface $entityManager, $id)
    {
        $commentRepo = $entityManager->getRepository(Comment::class);
        $comment = $commentRepo->find($id);

        if (!$comment) {
            throw $this->createNotFoundException('No comment found for id '.$id);
        }
            $entityManager->remove($comment);
            $entityManager->flush();

        return $this->redirectToRoute('app_comment_index');
    }
}
