<?php

namespace App\Controller;

use App\Entity\Regime;
use App\Form\RegimeType;
use App\Repository\RegimeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/regime', name: 'regime_')]
class RegimeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(RegimeRepository $regimeRepository, Request $request, PaginatorInterface $paginator): Response
    {
        {
            $query = $regimeRepository->findAll();
            $regimes = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                5
            );
    
            return $this->render('regime/index.html.twig', [
                'regimes' => $regimes,
            ]);
        }
    }

    #[Route('/add', name: 'add')]
    public function new(Request $request ,EntityManagerInterface $manager): Response
    {
        {
            if (!$this->isGranted('ROLE_ADMIN')) {
                throw new AccessDeniedException('Access denied. You need to have the ROLE_ADMIN role.');
            }
    
            $regime = new Regime();
            $form = $this->createForm(RegimeType::class, $regime);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $manager->persist($regime);
                $manager->flush();
                
                $this->addFlash(
                    'success',
                    'Le menu a été ajoutée avec succès. '
                    );
                return $this->redirectToRoute('regime_index');
            }
    
            return $this->render('regime/new.html.twig', [
                'regime' => $regime,
                'form' => $form,
            ]);
    }
}

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Regime $regime, Request $request, EntityManagerInterface $manager): Response
    {
        
            if (!$this->isGranted('ROLE_ADMIN')) {
                throw new AccessDeniedException('Access denied. You need to have the ROLE_ADMIN role.');
            }

            $form = $this->createForm(RegimeType::class, $regime);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $manager->persist($regime);
                $manager->flush();
    
                $this->addFlash(
                    'success',
                    'Le menu a été modifiée avec succès. '
                    );
                return $this->redirectToRoute('regime_index');
            }
    
            return $this->render('regime/edit.html.twig', [
                'regime' => $regime,
                'form' => $form,
            ]);
    
    }

    #[Route('/{id}', name: 'show')]
    public function show(Regime $regime): Response
    {
        return $this->render('regime/show.html.twig', [
            'regime' => $regime,
        ]);
    }


    #[Route('/{id}/delete', name: 'delete')]
    public function delete(RegimeRepository $regimeRepository, Regime $regime, EntityManagerInterface $manager): Response
    {

        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied. You need to have the ROLE_ADMIN role.');
        }
        
        $id = $regime->getId();
        $r = $regimeRepository->find($id);
        // dd($r);
        if (!$r) {
            throw $this->createNotFoundException('No regim found for id '.$id);
        }
        $manager->remove($r);
        $manager->flush();

        $this->addFlash(
        'success',
        'Le régime a été supprimé avec succès. '
        );
        return $this->redirectToRoute('regime_index');

    }


}
