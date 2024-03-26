<?php
namespace App\Controller\admin;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Description of CategoriesController
 *
 * @author LOUIZA
 */
class AdminCategoriesController extends AbstractController {

    /**
     * 
     * @var FormationRepository
     */
    private $formationRepository;

    /**
     * 
     * @var CategorieRepository
     */
    private $categorieRepository;

    /**
     * Création du constructeur
     * @param FormationRepository $formationRepository
     * @param CategorieRepository $categorieRepository
     */
    function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }

    /**
     * Affiche la liste des catégories.
     * Cette méthode récupère le terme de recherche (s'il existe) à partir de la requête,
     * @Route("/admin/categories", name="admin.categories")
     * @return Response
     */
    

    public function index(Request $request): Response
    {
        $searchTerm = $request->query->get('search');

        if ($searchTerm) {
            $categories = $this->categorieRepository->findBySearchTerm($searchTerm);
        } else {
            $categories = $this->categorieRepository->findAll();
        }

        $formations = $this->formationRepository->findAll();

        return $this->render("/admin/admin.categories.html.twig", [
            'formations' => $formations,
            'categories' => $categories,
        ]);
    }



    /**
     * Suppression d'une catégorie
     * Redirection vers la page d'administration
     * @Route("/admin/categories/suppr/{id}", name="admin.categories.suppr")
     * @param Categorie $categorie
     * @return Response
     */
    public function suppr(Categorie $categorie): Response{
        $this->categorieRepository->remove($categorie, true);
        return $this->redirectToRoute('admin.categories');
    }

    /**
     * Ajout d'une catégorie 
     * Redirection vers la page d'administration
     * @Route("/admin/categories/ajout", name="admin.ajout.categorie")
     * @param Request $request
     * @return Response
     */
    public function ajout(Request $request): Response{
        $name = $request->get("name");
        $nomcategorie = $this->categorieRepository->findAllEqual($name);

        if ($nomcategorie == false) {
            $categories = new Categorie();
            $categories->setName($name);
            $this->categorieRepository->add($categories, true);
            return $this->redirectToRoute('admin.categories');
        }
        return $this->redirectToRoute('admin.categories');
    }

    /**
     * Tri les enregistrements selon le champ
     * @Route("/admin/categories/tri/{champ}/{ordre}", name="admin.categories.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre): Response{
        $categories = $this->categorieRepository->findAllOrderBy($champ, $ordre);
        $formations = $this->formationRepository->findAll();
        return $this->render('/admin/admin.categories.html.twig', [
            'formations' => $formations,
            'categories' => $categories,
        ]);
    }
}

