<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\DietRecommendationFormType;
use App\Repository\UserRepository;
use App\Service\DietRecommendation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/diet', name: 'diet_')]
class DietController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('diet/index.html.twig', [
            'controller_name' => 'DietController',
        ]);
    }

    #[Route("/recommendation", name: "recommendation")]
    public function recommendDiet(Request $request, DietRecommendation $dietRecommendation, UserRepository $userRepository): Response
    {
        // if (!$this->isGranted('ROLE_USER')) {
        //     throw new AccessDeniedException('Access denied. You need to have the ROLe user role.');
        // }

        $user = new User();   
        if (!$user=$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        
        $form = $this->createForm(DietRecommendationFormType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $caloriesNeeded = $dietRecommendation->calculateCaloriesNeeded($user);

            return $this->render('diet/recommendation_result.html.twig', [
                'caloriesNeeded' => $caloriesNeeded,
                'user' => $user
            ]);
        }

        return $this->render('diet/recommendation_form.html.twig', [
            'form' => $form->createView(),
            // 'user' => $userRepository->findAll()
        ]);
    }

}
