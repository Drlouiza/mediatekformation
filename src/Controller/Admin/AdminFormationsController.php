<?php

namespace App\Controller\Admin;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Description of AdminFormationsController
 *
 * @author louiza
 */

class AdminFormationsController extends AbstractController
{
    const PAGE_FORMATIONS = "pages/admin/formations.html.twig";

    const PAGE_FORMATION = "pages/admin/formation.html.twig";

    const PAGE_FORMATION_VU = "pages/admin/formationVu.html.twig";

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
    
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }
    
    /**
     * @Route("/admin", name="admin.formations")
     * @return Response
     */
    public function index(): Response{
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/formations/tri/{champ}/{ordre}/{table}", name="admin.formations.sort")
     * @param type $champ
     * @param type $ordre
     * @param type $table
     * @return Response
     */
    public function sort($champ, $ordre, $table=""): Response{
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
    /**
     * @Route("/admin/formations/recherche/{champ}/{table}", name="admin.formations.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContain($champ, Request $request, $table=""): Response{
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    /**
     * @Route("/admin/formations/formation/{id}", name="admin.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response{
        $formation = $this->formationRepository->find($id);
        return $this->render(self::PAGE_FORMATION_VU, [
            'formation' => $formation
        ]);
    }

    /**
     * Suppression d'une formation
     * @Route("/admin/formations/suppr/{id}", name="admin.formation.suppr")
     * @param Formation $formation
     * @return Response
     */
    public function suppr(Formation $formation): 
        Response{
        $this->formationRepository->remove($formation, true);
        $this->addFlash('danger','La suppression de la formation ' . $formation->getTitle() . ' a été effectuée avec succès.');
        return $this->redirectToRoute('admin.formations');
    }

    /**
     * Edition d'une formation
     * @Route("/admin/formation/edit/{id}", name="admin.formation.edit")
     * @param Formation $formation
     * @param Request $request
     * @return Response
     */
    public function edit(Formation $formation, Request $request): 
        Response{
        $formFormation = $this->createForm(FormationType::class, $formation);

        $formFormation->handleRequest($request);
        if($formFormation->isSubmitted() && $formFormation->isValid()){
            $this->formationRepository->add($formation, true);
            $this->addFlash('success', 'La modification de la formation ' . $formation->getTitle() . ' a été effectuée avec succès.');
            return $this->redirectToRoute('admin.formations');
        }

        return $this->render(self::PAGE_FORMATION, [
            'formation' => $formation,
            'formFormation' => $formFormation->createView()
        ]);
    }
    
    /**
     * Ajouter une formation
     * @Route("/admin/formation/ajout", name="admin.formation.ajout")
     * @param Request $request
     * @return Response
     */
    public function ajout(Request $request): 
        Response{
        $formation = new Formation();
        $formFormation = $this->createForm(FormationType::class, $formation);

        $formFormation->handleRequest($request);
        if($formFormation->isSubmitted() && $formFormation->isValid()){
            $this->formationRepository->add($formation, true);
            $this->addFlash('success','La formation ' . $formation->getTitle() . ' a été ajoutée avec succès.');
            return $this->redirectToRoute('admin.formations');
        }

        return $this->render(self::PAGE_FORMATION, [
            'formation' => $formation,
            'formFormation' => $formFormation->createView()
        ]);
    }
}
