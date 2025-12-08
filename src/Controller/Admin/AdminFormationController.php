<?php
namespace App\Controller\Admin;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controleur des formations
 *
 * @author emds
 */
class AdminFormationController extends AbstractController {

    const PAGE_FORMATIONS = "pages/admin/formations.html.twig";
    
    const PAGE_FORMATION = "pages/admin/formation.html.twig";

    const VUE_FORMATION = "pages/admin/vue.formation.html.twig";



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
    
    #[Route('/admin/formations', name: 'admin.formations')]
    public function index(): Response{
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    #[Route('/admin/formations/tri/{champ}/{ordre}/{table}', name: 'admin.formations.sort')]
    public function sort($champ, $ordre, $table=""): Response{
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    #[Route('/admin/formations/recherche/{champ}/{table}', name: 'admin.formations.findallcontain')]
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

    #[Route('/admin/formations/formation/{id}', name: 'admin.formations.showone')]
    public function showOne($id): Response{
        $formation = $this->formationRepository->find($id);
        return $this->render(self::VUE_FORMATION, [
            'formation' => $formation
        ]);
    }


    /*
    * Supprime une formation en base de données.
    * Vérifie d'abord que la formation existe pour éviter les erreurs.
    * Ajoute un message flash pour informer l'utilisateur.
    * Redirige vers la page d'administration des formations.
    */
    #[Route('admin/formations/delete/{id}' , name: 'admin.formations.delete')]
    public function delete($id): Response {
        $formation = $this->formationRepository->find($id);
        if (!$formation) {
            $this->addFlash('warning', 'La formation demandée n’existe pas.');
            return $this->redirectToRoute('admin.formations');
        }
        $this->formationRepository->remove($formation, true);
        $this->addFlash('danger', 'La suppression de la formation "' . $formation->getTitle() . '" a été effectuée avec succès.');

        return $this->redirectToRoute('admin.formations');
    }
    

    #[Route('admin/formations/add' , name: 'admin.formations.add')]
    public function createformation(Request $request): Response {
       $formation = new Formation;
       $form = $this->createform(FormationType::class, $formation);
       $form->handleRequest($request);
       
       if($form->isSubmitted() && $form->isValid()){
            $this->formationRepository->add($formation , true );
            $this->addFlash(
                    'success',
                    'Ajout de la formation ' . $formation->getTitle() . ' prise en compte');
            return $this->redirectToRoute('admin.formations');
       }
        return $this->render(self::PAGE_FORMATION, [
            'formation' => $formation,
            'formFormation' => $form->createView()
        ]);
       }

       /**
     * @param Formation $formation
     * @param Request $request
     * @return Response
     */
    #[Route('admin/formations/edit{id}' , name: 'admin.formations.edit')]
        public function editformation (formation $formation, request $request): Response {
            $formformation = $this->createForm(FormationType::class, $formation);
            $formformation->handleRequest($request);
            
            if($formformation->isSubmitted() && $formformation->isValid()){
                $this->formationRepository->add($formation , true );
                $this->addFlash(
                        'success',
                        'Modification de la formation ' . $formation->getTitle() . ' prise en compte');
                return $this->redirectToRoute('admin.formations');
            }
            return $this->render(self::PAGE_FORMATION, [
                'formation' => $formation,
                'formFormation' => $formformation->createView()
            ]);
        }

}