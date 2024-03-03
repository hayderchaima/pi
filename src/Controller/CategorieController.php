<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'app_categorie')]
    public function index(): Response
    {
        return $this->render('admin.html.twig', [
            'controller_name' => 'CategorieController',
        ]);
    }
    #[Route('/add_categorie', name: 'add_categorie')]

    public function Add(Request  $request , ManagerRegistry $doctrine ) : Response {

        $Categorie =  new Categorie() ;
        $form =  $this->createForm(CategorieType::class,$Categorie) ;
        $form->add('Ajouter' , SubmitType::class) ;
        $form->handleRequest($request) ;
        if($form->isSubmitted()&& $form->isValid()){
            
            $Categorie = $form->getData();
            $em= $doctrine->getManager() ;
            $em->persist($Categorie);
            $em->flush();
            return $this ->redirectToRoute('add_categorie') ;
        }
        return $this->render('categorie/addcategories.html.twig' , [
            'form' => $form->createView()
        ]) ;
    }

    #[Route('/afficher_recla', name: 'afficher_recla')]
    public function AfficheCategorie (CategorieRepository $repo ,PaginatorInterface $paginator ,Request $request    ): Response
    {
        //$repo=$this ->getDoctrine()->getRepository(Categorie::class) ;
        $Categorie=$repo->findAll() ;
         $pagination = $paginator->paginate(
            $Categorie,
            $request->query->getInt('page', 1),
            1
            
        );
        return $this->render('categorie/index.html.twig' , [
            'Categorie' => $pagination ,
            'ajoutA' => $Categorie
        ]) ;
    }

    #[Route('/delete_rec/{id}', name: 'delete_rec')]
    public function Delete($id,CategorieRepository $repository , ManagerRegistry $doctrine) : Response {
        $Categorie=$repository->find($id) ;
        $em=$doctrine->getManager() ;
        $em->remove($Categorie);
        $em->flush();
        return $this->redirectToRoute("afficher_recla") ;

    }
    #[Route('/update_rec/{id}', name: 'update_rec')]
    function update(CategorieRepository $repo,$id,Request $request , ManagerRegistry $doctrine){
        $Categorie = $repo->find($id) ;
        $form=$this->createForm(CategorieType::class,$Categorie) ;
        $form->add('update' , SubmitType::class) ;
        $form->handleRequest($request) ;
        if($form->isSubmitted()&& $form->isValid()){

            $Categorie = $form->getData();
            $em=$doctrine->getManager() ;
            $em->flush();
            return $this ->redirectToRoute('afficher_recla') ;
        }
        return $this->render('categorie/updatecategories.html.twig' , [
            'form' => $form->createView()
        ]) ;

    
    }

    #[Route('/statsabonn', name: 'statsabonn')]
    public function statistiques(CategorieRepository $abonnrepo){
        // On va chercher toutes les catégories
        $abonn = $abonnrepo->findAll();

        $abonnName = [];
        $abonnColor = [];
        $abonnCount = [];

        // On "démonte" les données pour les séparer tel qu'attendu par ChartJS
        foreach($abonn as $abon){
            $abonnName[] = $abon->getName();
            $abonnColor[] = $abon->getColor();
            $abonnCount[] = count($abon->getReclamations());
        }

        // On va chercher le nombre d'annonces publiées par date

        return $this->render('stat/stats.html.twig', [
            'abonnName' => json_encode($abonnName),
            'abonnColor' => json_encode($abonnColor),
            'abonnCount' => json_encode($abonnCount),
        ]);
    }
}
