<?php

namespace App\Controller\Admin\Trick;

use App\Entity\Trick;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ShowController extends AbstractController
{
    #[Route('/admin/trick/show/{id}', name: 'admin_trick_show')]
    public function show(Trick $trick)
    {
        return $this->render('admin/trick/show.html.twig', [
            'trick' => $trick,
            'current' => 'tricks',
        ]);
    }
}
