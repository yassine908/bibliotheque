<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategorieController extends AbstractController
{
    // Afficher les livres filtrés par catégorie
    #[Route('/categorie/{id}', name: 'app_categorie_livres')]
    public function livresParCategorie(int $id, CategorieRepository $categorieRepository, LivreRepository $livreRepository): Response
    {
        // Chercher la catégorie
        $categorie = $categorieRepository->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }

        // Chercher les livres de cette catégorie
        $livres = $livreRepository->findBy(['categorie' => $categorie]);

        // Chercher toutes les catégories pour le menu
        $categories = $categorieRepository->findAll();

        return $this->render('categorie/index.html.twig', [
            'categorie' => $categorie,
            'livres' => $livres,
            'categories' => $categories,
        ]);
    }
}
