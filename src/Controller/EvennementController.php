<?php

namespace App\Controller;

use App\Entity\Evennement;
use App\Form\EvennementType;
use App\Repository\EvennementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;



#[Route('/evennement')]
class EvennementController extends AbstractController
{

    public function __construct(private LoggerInterface $logger, private SerializerInterface $serializer)
{

}

#[Route('/search', name: 'app_evennement_search', methods: ['GET'])]
public function searchByName(Request $request, EvennementRepository $repository): Response
{
    $name = $request->query->get('name');
    $evennements = $repository->findByName($name);

    return $this->render('evennement/indexBack.html.twig', [
        'evennements' => $evennements,
        'searchQuery' => $name,
        
    ]);
}
#[Route('/sort', name: 'app_evennement_sort', methods: ['GET'])]
public function sortBy(Request $request, EvennementRepository $repository): Response
{
    $sortBy = $request->query->get('sortBy');
    $evennements = $repository->findAllSortedBy($sortBy);

    // Get the search query from the previous request (assuming it persists)
    $searchQuery = $request->getSession()->get('searchQuery');

    return $this->render('evennement/indexBack.html.twig', [
        'evennements' => $evennements,
        'sortBy' => $sortBy,
        // Pass the retrieved search query to the template
        'searchQuery' => $searchQuery,
    ]);
}


    #[Route('/stat', name: 'app_evennement_statistics', methods: ['GET'])]
public function statistics(EvennementRepository $evennementRepository): Response
{
    $evenements = $evennementRepository->findAll();

    // Prepare data for chart
    $chartData = [];
    foreach ($evenements as $evenement) {
        $chartData[] = [
            'nom' => $evenement->getNom(),
            'nbParticipant' => $evenement->getNbParticipant(),
        ];
    }

    return $this->render('evennement/statistics.html.twig', [
        'chartData' => json_encode($chartData), // Encode data as JSON for Twig 
    ]);
}

    #[Route('/', name: 'app_evennement_index', methods: ['GET'])]
    public function index(EvennementRepository $evennementRepository): Response
    {
        return $this->render('evennement/index.html.twig', [
            'evennements' => $evennementRepository->findAll(),
        ]);
    }
    #[Route('/back', name: 'app_evennement_index_back', methods: ['GET'])]
    public function indexBack(EvennementRepository $evennementRepository): Response
    {
        return $this->render('evennement/indexback.html.twig', [
            'evennements' => $evennementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_evennement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evennement = new Evennement();
        $form = $this->createForm(EvennementType::class, $evennement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {

                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('event_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                }

                $evennement->setImage($newFilename);
            }

            $entityManager->persist($evennement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evennement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evennement/new.html.twig', [
            'evennement' => $evennement,
            'form' => $form,
        ]);
    }
    #[Route('/newBack', name: 'app_evennement_new_back', methods: ['GET', 'POST'])]
    public function newBack(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evennement = new Evennement();
        $form = $this->createForm(EvennementType::class, $evennement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {

                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('event_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                }

                $evennement->setImage($newFilename);
            }
            $entityManager->persist($evennement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evennement_index_back', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evennement/newBack.html.twig', [
            'evennement' => $evennement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evennement_show', methods: ['GET'])]
    public function show(Evennement $evennement): Response
    {
        return $this->render('evennement/show.html.twig', [
            'evennement' => $evennement,
        ]);
    }

    #[Route('/{id}/back', name: 'app_evennement_show_back', methods: ['GET'])]
    public function showBack(Evennement $evennement): Response
    {
        return $this->render('evennement/showBack.html.twig', [
            'evennement' => $evennement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evennement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evennement $evennement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvennementType::class, $evennement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {

                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('event_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                }

                $evennement->setimage($newFilename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_evennement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evennement/edit.html.twig', [
            'evennement' => $evennement,
            'form' => $form,
        ]);
    }
    #[Route('/{id}/edit/back', name: 'app_evennement_edit_back', methods: ['GET', 'POST'])]
    public function editBack(Request $request, Evennement $evennement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvennementType::class, $evennement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {

                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('event_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                }

                $evennement->setImage($newFilename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_evennement_index_back', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evennement/editBack.html.twig', [
            'evennement' => $evennement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evennement_delete', methods: ['POST'])]
    public function delete(Request $request, Evennement $evennement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $evennement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evennement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evennement_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/back', name: 'app_evennement_delete', methods: ['POST'])]
    public function deleteBack(Request $request, Evennement $evennement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $evennement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evennement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evennement_index_back', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/pdf', name: 'app_event_PDF', methods: ['GET'])]
    public function PDF(Evennement $evennement)
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);



        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('evennement/pdfEvennement.html.twig', [
            'evennement' => $evennement,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        $output = $dompdf->output();

        $publicDirectory = $this->getParameter('evennement_pdf_directory');

        $pdfFilepath =  $publicDirectory . '/' . uniqid() . '.pdf';
        if (!file_exists($pdfFilepath)) {
            file_put_contents($pdfFilepath, $output);
            return new Response("The PDF file has been succesfully generated !");
        } else {
            return new Response("The PDF file already exist");
        }



        // Output the generated PDF to Browser (force download)
    }
    

 
}