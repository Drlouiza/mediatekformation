<?php

namespace App\Controller\Admin;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of PlaylistsController
 *
 * @author emds
 */
class AdminPlaylistController extends AbstractController {

    const PAGE_PLAYLIST = "pages/admin/playlist.html.twig";
    
    const PAGE_PLAYLISTS = "pages/admin/playlists.html.twig";

    /**
     *
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
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
    
    public function __construct(PlaylistRepository $playlistRepository,
            CategorieRepository $categorieRepository,
            FormationRepository $formationRespository) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }
    
    /**
     * @Route("/admin/playlists", name="admin.playlists")
     * @return Response
     */
    #[Route('/admin/playlist', name: 'admin.playlists')]
    public function index(): Response{
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    #[Route('/admin/playlists/tri/{champ}/{ordre}', name: 'admin.playlists.sort')]
    public function sort($champ, $ordre): Response{
        if($champ == "name"){
            $playlists = $this->playlistRepository->findAllOrderByName($ordre);
        }
        if($champ == "nombre"){
            $playlists = $this->playlistRepository->findAllOrderByAmount($ordre);
        }
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    #[Route('/admin/playlists/recherche/{champ}/{table}', name: 'admin.playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response{
        $valeur = $request-> get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    #[Route('/admin/playlists/playlist/{id}', name: 'admin.playlists.showone')]
    public function showOne($id): Response{
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render(self::PAGE_PLAYLIST, [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);
    }

    /*
    * Supprime une playlist en base de données.
    * Vérifie d'abord que la playlist ne contient aucune formation.
    * Ajoute un message flash pour informer l'utilisateur.
    * Redirige vers la page d'administration des formations.
    */
    #[Route('admin/playlists/delete/{id}' , name: 'admin.playlists.delete')]
    public function delete($id): Response {
        $playlist = $this->playlistRepository->find($id);
        if (!$playlist) {
            $this->addFlash('warning', 'Playlist introuvable.');
            return $this->redirectToRoute("admin.playlists");
        }
        if (!$playlist->getFormations()->isEmpty()){
            $this->addFlash('warning', 'Impossible de supprimer la playlist ' .$playlist->getName() .', car elle contient des formations.');
            return $this->redirectToRoute("admin.playlists");
        }
        $this->playlistRepository->remove($playlist, true);
        $this->addFlash('danger', 'La suppression de la formation "' . $playlist->getName() . '" a été effectuée avec succès.');

        return $this->redirectToRoute('admin.formations');
    }
    
    /**
     * @param Playlist $playlist
     * @param Request $request
     * @return Response
     */
    #[Route('admin/playlist/edit{id}' , name: 'admin.playlists.edit')]
        public function editplaylist (Playlist $playlist, request $request): Response {
            $formPlaylist  = $this->createForm(PlaylistType::class, $playlist);
            $formPlaylist ->handleRequest($request);
            
            if($formPlaylist ->isSubmitted() && $formPlaylist ->isValid()){
                $this->playlistRepository->add($playlist, true );
                $this->addFlash(
                        'success',
                        'Modification de la playlist' . $playlist->getName() . ' prise en compte');
                return $this->redirectToRoute('admin.playlists');
            }
            return $this->render(self::PAGE_PLAYLIST, [
                'playlist' => $playlist,
                'formPlaylist' => $formPlaylist ->createView()
            ]);
        }

        
    #[Route('admin/playlists/add' , name: 'admin.playlists.add')]
    public function createplaylist(Request $request): Response {
       $playlist = new Playlist;
       $form = $this->createForm(PlaylistType::class, $playlist);
       $form->handleRequest($request);
       
       if($form->isSubmitted() && $form->isValid()){
            $this->playlistRepository->add($playlist, true );
            $this->addFlash(
                    'success',
                    'Ajout de la playlist ' . $playlist->getName() . ' prise en compte');
            return $this->redirectToRoute('admin.playlists');
       }
        return $this->render(self::PAGE_PLAYLIST, [
            'playlist' => $playlist,
            'formPlaylist' => $form->createView()
        ]);
       }
}

