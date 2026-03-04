<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Repository\CategorieRepository;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LivreController extends AbstractController
{
    // Afficher tous les livres
    #[Route('/', name: 'app_home')]
    #[Route('/livre', name: 'app_livre')]
    #[Route('/livres', name: 'app_livre_index')]
    public function index(LivreRepository $livreRepository, CategorieRepository $categorieRepository): Response
    {
        $livres = $livreRepository->findAll();
        $categories = $categorieRepository->findAll();

        return $this->render('livre/index.html.twig', [
            'livres' => $livres,
            'categories' => $categories,
        ]);
    }

    // Ajouter un nouveau livre
    #[Route('/livre/nouveau', name: 'app_livre_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, CategorieRepository $categorieRepository): Response
    {
        if ($request->isMethod('POST')) {
            $titre = $request->request->get('titre');
            $auteur = $request->request->get('auteur');
            $annee = $request->request->get('annee');
            $resume = $request->request->get('resume');
            $disponible = $request->request->get('disponible') ? true : false;
            $categorieId = $request->request->get('categorie');

            if ($titre && $auteur && $annee) {
                $livre = new Livre();
                $livre->setTitre($titre);
                $livre->setAuteur($auteur);
                $livre->setAnnee($annee);
                $livre->setResume($resume);
                $livre->setDisponible($disponible);

                // Associer la catégorie si elle est choisie
                if ($categorieId) {
                    $categorie = $categorieRepository->find($categorieId);
                    $livre->setCategorie($categorie);
                }

                $entityManager->persist($livre);
                $entityManager->flush();

                $this->addFlash('success', 'Livre ajouté avec succès !');

                return $this->redirectToRoute('app_livre_index');
            }
        }

        // Récupérer toutes les catégories pour le formulaire
        $categories = $categorieRepository->findAll();

        return $this->render('livre/new.html.twig', [
            'categories' => $categories,
        ]);
    }

    // Voir un livre
    #[Route('/livre/{id}', name: 'app_livre_show')]
    public function show(Livre $livre): Response
    {
        return $this->render('livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }

    // Modifier un livre
    #[Route('/livre/{id}/modifier', name: 'app_livre_edit')]
    public function edit(Request $request, Livre $livre, EntityManagerInterface $entityManager, CategorieRepository $categorieRepository): Response
    {
        if ($request->isMethod('POST')) {
            $titre = $request->request->get('titre');
            $auteur = $request->request->get('auteur');
            $annee = $request->request->get('annee');
            $resume = $request->request->get('resume');
            $disponible = $request->request->get('disponible') ? true : false;
            $categorieId = $request->request->get('categorie');

            if ($titre && $auteur && $annee) {
                $livre->setTitre($titre);
                $livre->setAuteur($auteur);
                $livre->setAnnee($annee);
                $livre->setResume($resume);
                $livre->setDisponible($disponible);

                // Associer la catégorie
                if ($categorieId) {
                    $categorie = $categorieRepository->find($categorieId);
                    $livre->setCategorie($categorie);
                } else {
                    $livre->setCategorie(null);
                }

                $entityManager->flush();

                $this->addFlash('success', 'Livre modifié avec succès !');

                return $this->redirectToRoute('app_livre_index');
            }
        }

        // Récupérer toutes les catégories pour le formulaire
        $categories = $categorieRepository->findAll();

        return $this->render('livre/edit.html.twig', [
            'livre' => $livre,
            'categories' => $categories,
        ]);
    }

    // Supprimer un livre
    #[Route('/livre/{id}/supprimer', name: 'app_livre_delete', methods: ['POST'])]
    public function delete(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($livre);
            $entityManager->flush();
            $this->addFlash('success', 'Livre supprimé avec succès !');
        }

        return $this->redirectToRoute('app_livre_index');
    }
}