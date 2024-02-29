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
use Symfony\Component\String\Slugger\SluggerInterface;

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

    public function Add(Request  $request , ManagerRegistry $doctrine ,SluggerInterface $slugger) : Response {

        $Categorie =  new Categorie() ;
        $form =  $this->createForm(CategorieType::class,$Categorie) ;
        $form->add('Ajouter' , SubmitType::class) ;
        $form->handleRequest($request) ;
        if($form->isSubmitted()&& $form->isValid()){
            $brochureFile = $form->get('image')->getData();
            //$file =$Categorie->getImage();
            $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
            //$uploads_directory = $this->getParameter('upload_directory');
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
            //$fileName = md5(uniqid()).'.'.$file->guessExtension();
            $brochureFile->move(
                $this->getParameter('upload_directory'),
                $newFilename
            );
            $Categorie->setImage(($newFilename));
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
    public function AfficheCategorie (CategorieRepository $repo   ): Response
    {
        //$repo=$this ->getDoctrine()->getRepository(Categorie::class) ;
        $Categorie=$repo->findAll() ;
        return $this->render('categorie/index.html.twig' , [
            'Categorie' => $Categorie ,
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
}
