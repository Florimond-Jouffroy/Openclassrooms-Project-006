<?php

namespace App\Controller\Admin\Trick;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/admin/tricks', name: 'admin_tricks')]
    public function index(TrickRepository $trickRepository): Response
    {
        $tricks = $trickRepository->findAll();

        return $this->render('admin/trick/tricks.html.twig', [
            'current' => 'tricks',
            'tricks' => $tricks,
        ]);
    }
}
