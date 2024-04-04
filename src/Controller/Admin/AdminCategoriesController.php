<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Description of CategoriesController
 *
 * @author LOUIZA
 */

class AdminCategoriesController extends AbstractController
{
    const PAGE_CATEGORIES = "pages/admin/categories.html.twig";

    const PAGE_CATEGORIE = "pages/admin/categorie.html.twig";
    
    /**
     *
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    public function __construct(CategorieRepository $categorieRepository) {
        $this->categorieRepository = $categorieRepository;
    }
    
    /**
     * Création du constructeur
     * @Route("/admin/categories", name="admin.categories")
     * @return Response
     */
    public function index(): Response{
        $categories = $this->categorieRepository->findAllOrderByName('ASC');
        return $this->render(self::PAGE_CATEGORIES, [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/categories/tri/{champ}/{ordre}", name="admin.categories.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre): Response{
        if($champ == "name"){
            $categories = $this->categorieRepository->findAllOrderByName($ordre);
        }
        if($champ == "nombre"){
            $categories = $this->categorieRepository->findAllOrderByAmount($ordre);
        }
        return $this->render(self::PAGE_CATEGORIES, [
            'categories' => $categories
        ]);
    }
    
    /**
     * @Route("/admin/categories/recherche/{champ}/{table}", name="admin.categories.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContain($champ, Request $request, $table=""): Response{
        $valeur = $request->get("recherche");
        $categories = $this->categorieRepository->findByContainValue($champ, $valeur, $table);
        return $this->render(self::PAGE_CATEGORIES, [
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    /**
     * @Route("/admin/categorie/edit/{id}", name="admin.categorie.edit")
     * @param Categorie $categorie
     * @param Request $request
     * @return Response
     */
    public function edit(Categorie $categorie, Request $request): Response
{
    // Récupération des formations initiales liées à la catégorie
    $formationsIni = $categorie->getFormations()->toArray();

    // Création du formulaire de modification de catégorie
    $formCategorie = $this->createForm(CategorieType::class, $categorie);
    $formCategorie->handleRequest($request);

    // Traitement du formulaire s'il est soumis et valide
    if ($formCategorie->isSubmitted() && $formCategorie->isValid()) {
        // Enregistrement des modifications de la catégorie
        $this->categorieRepository->add($categorie, true);

        // Récupération des formations après modification
        $formations = $categorie->getFormations()->toArray();

        // Ajout des nouvelles formations liées à la catégorie
        foreach ($formations as $formation) {
            if (!in_array($formation, $formationsIni)) {
                $this->categorieRepository->addFormationCategorie($formation->getId(), $categorie->getId());
            }
        }

        // Suppression des formations retirées de la catégorie
        foreach ($formationsIni as $formation) {
            if (!in_array($formation, $formations)) {
                $this->categorieRepository->delFormationCategorie($formation->getId(), $categorie->getId());
            }
        }

        // Ajout d'un message de succès
        $this->addFlash( 'success', 'La modification de la catégorie ' . $categorie->getName() . ' a été effectuée avec succès.'
        );

        // Redirection vers la liste des catégories
        return $this->redirectToRoute('admin.categories');
    }

    // Affichage du formulaire de modification de catégorie
    return $this->render(self::PAGE_CATEGORIE, [
        'categorie' => $categorie,
        'formCategorie' => $formCategorie->createView()
    ]);
}

    /**
     * Ajout d'une catégorie
     * @Route("/admin/categorie/ajout", name="admin.categorie.ajout")
     * @param Request $request
     * @return Response
     */
    public function ajout(Request $request): Response
    {
        $categorie = new Categorie();
        $formCategorie = $this->createForm(CategorieType::class, $categorie);

        $formCategorie->handleRequest($request);
        if ($formCategorie->isSubmitted() && $formCategorie->isValid()){
            $this->categorieRepository->add($categorie, true);
            $formations = $categorie->getFormations()->toArray();
            foreach($formations as $formation) {
                $this->categorieRepository->addFormationCategorie($formation->getId(), $categorie->getId());
            }
            $this->addFlash( 'success',  'La categorie ' . $categorie->getName() . " a été ajoutée avec succès."
            );
            return $this->redirectToRoute('admin.categories');
        }

        return $this->render(self::PAGE_CATEGORIE, [
            'categorie' => $categorie,
            'formCategorie' => $formCategorie->createView()
        ]);
    }
    
    /**
     * Suppression d'une catégorie
     * @Route("/admin/categorie/suppr/{id}", name="admin.categorie.suppr")
     * @param Playlist $playlist
     * @return Response
     */
    public function suppr(Categorie $categorie): Response
    {
        if(count($categorie->getFormations()) > 0){
            $this->addFlash( 'danger', 'Impossible de supprimer la categorie ' . $categorie->getName() . ' car elle contient des formations'
            );
            return $this->redirectToRoute('admin.categories');
        }
        
        $this->categorieRepository->remove($categorie, true);
        $this->addFlash('danger', 'La suppression de la categorie ' . $categorie->getName() . " a été effectuée avec succès."
        );
        return $this->redirectToRoute('admin.categories');
    }

}
