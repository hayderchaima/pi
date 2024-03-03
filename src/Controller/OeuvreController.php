<?php

namespace App\Controller;

use App\Entity\Oeuvre;
use App\Form\OeuvreType;
use App\Repository\OeuvreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/oeuvre')]
class OeuvreController extends AbstractController
{
    #[Route('/', name: 'app_oeuvre_index', methods: ['GET'])]
    public function index(Request $request,OeuvreRepository $oeuvreRepository, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $oeuvreRepository->createQueryBuilder('o')
        ->orderBy('o.id', 'ASC');

    // Get the Doctrine Query object
    $query = $queryBuilder->getQuery();
        $pagination = $paginator->paginate(
            $query, // Query to paginate
            $request->query->getInt('page', 1), // Current page number
            2 // Number of items per page
        );
        return $this->render('oeuvre/index.html.twig', [
            'pagination' => $pagination,
        ]);
       
    }
    #[Route('/back', name: 'app_oeuvre_index_back', methods: ['GET'])]
    public function indexback(OeuvreRepository $oeuvreRepository): Response
    {
        return $this->render('oeuvre/indexback.html.twig', [
            'oeuvres' => $oeuvreRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_oeuvre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $oeuvre = new Oeuvre();
        $form = $this->createForm(OeuvreType::class, $oeuvre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();

            if ($imageFile) {

                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('oeuvre_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                }

                $oeuvre->setImage($newFilename);
            }


            $entityManager->persist($oeuvre);
            $entityManager->flush();

            return $this->redirectToRoute('app_oeuvre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('oeuvre/new.html.twig', [
            'oeuvre' => $oeuvre,
            'form' => $form,
        ]);
    }
    #[Route('/newBack', name: 'app_oeuvre_new_back', methods: ['GET', 'POST'])]
    public function newBack(Request $request, EntityManagerInterface $entityManager): Response
    {
        $oeuvre = new Oeuvre();
        $form = $this->createForm(OeuvreType::class, $oeuvre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($oeuvre);
            $entityManager->flush();

            return $this->redirectToRoute('app_oeuvre_index_back', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('oeuvre/newback.html.twig', [
            'oeuvre' => $oeuvre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_oeuvre_show', methods: ['GET'])]
    public function show(Oeuvre $oeuvre): Response
    {
        return $this->render('oeuvre/show.html.twig', [
            'oeuvre' => $oeuvre,
        ]);
    }
    #[Route('/{id}/back', name: 'app_oeuvre_show_back', methods: ['GET'])]
    public function showBack(Oeuvre $oeuvre): Response
    {
        return $this->render('oeuvre/showback.html.twig', [
            'oeuvre' => $oeuvre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_oeuvre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Oeuvre $oeuvre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OeuvreType::class, $oeuvre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {

                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('oeuvre_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                }

                $oeuvre->setImage($newFilename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_oeuvre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('oeuvre/edit.html.twig', [
            'oeuvre' => $oeuvre,
            'form' => $form,
        ]);
    }
    #[Route('/{id}/edit/back', name: 'app_oeuvre_edit_back', methods: ['GET', 'POST'])]
    public function editBack(Request $request, Oeuvre $oeuvre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OeuvreType::class, $oeuvre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {

                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('oeuvre_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                }

                $oeuvre->setImage($newFilename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_oeuvre_index_back', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('oeuvre/editback.html.twig', [
            'oeuvre' => $oeuvre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_oeuvre_delete', methods: ['POST'])]
    public function delete(Request $request, Oeuvre $oeuvre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$oeuvre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($oeuvre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_oeuvre_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/back', name: 'app_oeuvre_delete_back', methods: ['POST'])]
    public function deleteBack(Request $request, Oeuvre $oeuvre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$oeuvre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($oeuvre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_oeuvre_index_back', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/searchOeuvres', name: 'search_oeuvres', methods: ['GET'])]
    public function searchOeuvres(Request $request, OeuvreRepository $oeuvreRepository): JsonResponse
    {
        // Retrieve the search query from the request
        $query = $request->query->get('query');

        // Perform the search operation based on the query
        $oeuvres = $oeuvreRepository->searchByName($query);

        // Return the search results as JSON response
        return $this->json($oeuvres);
    }
    #[Route('/oeuvre/pdf', name: 'app_oeuvre_pdf', methods: ['GET'])]
    public function pdf(OeuvreRepository $oeuvreRepository): Response
    {
        $oeuvres = $oeuvreRepository->findAll();
    
        // Generate HTML content for the list of oeuvres
        $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');
    
            // Instantiate Dompdf with our options
            $dompdf = new Dompdf($pdfOptions);
    
    
    
            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('oeuvre/pdf.html.twig', [
                'oeuvres' => $oeuvres,
            ]);
    
            // Load HTML to Dompdf
            $dompdf->loadHtml($html);
    
            // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
            $dompdf->setPaper('A4', 'portrait');
    
            // Render the HTML as PDF
            $dompdf->render();
    
            $output = $dompdf->output();
    
            $publicDirectory = $this->getParameter('oeuvre_pdf_directory');
    
            $pdfFilepath =  $publicDirectory . '/' . uniqid() . '.pdf';
            if (!file_exists($pdfFilepath)) {
                file_put_contents($pdfFilepath, $output);
                return $this->redirectToRoute('app_oeuvre_index', [], Response::HTTP_SEE_OTHER);
            } else {
                return new Response("The PDF file already exist");
            }
    
    
    
            // Output the generated PDF to Browser (force download)
    
        }
}
