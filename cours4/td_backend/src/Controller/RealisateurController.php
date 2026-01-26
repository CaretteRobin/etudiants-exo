<?php

namespace App\Controller;

use App\Repository\RealisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RealisateurController extends AbstractController
{
    #[Route('/realisateurs', name: 'app_realisateur_index')]
    public function index(RealisateurRepository $realisateurRepository): Response
    {
        return $this->render('realisateur/index.html.twig', [
            'realisateurs' => $realisateurRepository->findAll(),
        ]);
    }
}
