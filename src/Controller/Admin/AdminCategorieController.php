<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCategorieController extends AbstractController
{
    const PAGE_CATEGORIES = "pages/admin/categories.html.twig";
    const PAGE_CATEGORIE = "pages/admin/categorie.html.twig";
    
    /**
     *
     * @var CategorieRepository
     */
    private $categorieRepository;

    public function __construct( CategorieRepository $categorieRepository) {
        $this->categorieRepository= $categorieRepository;
    }

    #[Route('/admin/categories', name: 'admin.categories')]
    public function index(): Response{
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_CATEGORIES, [
            'categories' => $categories
        ]);
    }


    #[Route('/admin/Categories/tri/{champ}/{ordre}', name: 'admin.categories.sort')]
    public function sort($champ, $ordre): Response{
        if($champ == "name"){
            $categories = $this->categorieRepository->findAllOrderByName($ordre);
        }
        return $this->render(self::PAGE_CATEGORIES, [
            'categories' => $categories
        ]);
    }


    #[Route('/admin/categories/recherche/{champ}/{table}', name: 'admin.categories.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response{
        $valeur = $request->get("recherche");
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_CATEGORIES, [
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    /*
    * Supprime une categorie en base de données.
    * Vérifie d'abord que la categorie ne contient aucune formation.
    * Ajoute un message flash pour informer l'utilisateur.
    * Redirige vers la page d'administration des formations.
    */
    #[Route('admin/categories/delete/{id}' , name: 'admin.categories.delete')]
    public function delete($id): Response {
        $categorie = $this->categorieRepository->find($id);
        if (!$categorie) {
            $this->addFlash('warning', 'Categorie introuvable.');
            return $this->redirectToRoute('admin.categories');
        }
        if (!$categorie->getFormations()->isEmpty()){
            $this->addFlash('warning', 'Impossible de supprimer la categire ' .$categorie->getName() .', car elle contient des formations.');
            return $this->redirectToRoute('admin.categories');
        }
        $this->categorieRepository->remove($categorie, true);
        $this->addFlash('danger', 'La suppression de la formation "' . $categorie->getName() . '" a été effectuée avec succès.');

        return $this->redirectToRoute('admin.categories');
    }

    /**
     * @param categorie $categorie
     * @param Request $request
     * @return Response
     */
    #[Route('admin/categories/edit{id}' , name: 'admin.categories.edit')]
        public function editcategorie (Categorie $categorie, request $request): Response {
            $form = $this->createForm(CategorieType::class, $categorie);
            $form ->handleRequest($request);
            
            if($form ->isSubmitted() && $form ->isValid()){
                $this->categorieRepository->add($categorie, true );
                $this->addFlash(
                        'success',
                        'Modification de la playlist' . $categorie->getName() . ' prise en compte');
                return $this->redirectToRoute('admin.categories');
            }
            return $this->render(self::PAGE_CATEGORIE, [
                'categorie' => $categorie,
                'formCategorie' => $form ->createView()
            ]);
        }

        #[Route('admin/categories/add' , name: 'admin.categories.add')]
        public function createcategorie(Request $request): Response {
            $categorie = new Categorie;
            $form = $this->createForm(CategorieType::class, $categorie);
            $form->handleRequest($request);
            
            if($form->isSubmitted() && $form->isValid()){
                    $this->categorieRepository->add($categorie, true );
                    $this->addFlash(
                            'success',
                            'Ajout de la categorie ' . $categorie->getName() . ' prise en compte');
                    return $this->redirectToRoute('admin.categories');
            }
            return $this->render(self::PAGE_CATEGORIE, [
                'categorie' => $categorie,
                'formCategorie' => $form->createView()
             ]);
       }
}
