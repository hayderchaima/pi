<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'app_reclamation')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'ReclamationController',
        ]);
    }
    #[Route('/add_reclamation', name: 'add_reclamation')]

    public function Add(Request  $request , ManagerRegistry $doctrine ,SluggerInterface $slugger) : Response {
        $Reclamation =  new Reclamation() ;
        $form =  $this->createForm(ReclamationType::class,$Reclamation) ;
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
            $Reclamation->setImage(($newFilename));
            $Reclamation = $form->getData();
            $em= $doctrine->getManager() ;
            $em->persist($Reclamation);
            $em->flush();
            return $this ->redirectToRoute('add_reclamation') ;
        }
        return $this->render('reclamation/frontadd.html.twig' , [
            'form' => $form->createView()
        ]) ;
    }

    #[Route('/afficher_rep', name: 'afficher_rep')]
    public function AfficheReclamation (ReclamationRepository $repo   ): Response
    {
        //$repo=$this ->getDoctrine()->getRepository(Reclamation::class) ;
        $Reclamation=$repo->findAll() ;
        return $this->render('reclamation/index.html.twig' , [
            'Reclamation' => $Reclamation ,
            'ajoutA' => $Reclamation
        ]) ;
    }

    #[Route('/delete_rep/{id}', name: 'delete_rep')]
    public function Delete($id,ReclamationRepository $repository , ManagerRegistry $doctrine) : Response {
        $Reclamation=$repository->find($id) ;
        $em=$doctrine->getManager() ;
        $em->remove($Reclamation);
        $em->flush();
        return $this->redirectToRoute("afficher_rep") ;

    }
    #[Route('/update_rep/{id}', name: 'update_rep')]
    function update(ReclamationRepository $repo,$id,Request $request , ManagerRegistry $doctrine){
        $Reclamation = $repo->find($id) ;
        $form=$this->createForm(ReclamationType::class,$Reclamation) ;
        $form->add('update' , SubmitType::class) ;
        $form->handleRequest($request) ;
        if($form->isSubmitted()&& $form->isValid()){

            $Reclamation = $form->getData();
            $em=$doctrine->getManager() ;
            $em->flush();
            return $this ->redirectToRoute('afficher_rep') ;
        }
        return $this->render('reclamation/update.html.twig' , [
            'form' => $form->createView()
        ]) ;

    }
}
