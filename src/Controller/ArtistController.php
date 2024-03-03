<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Form\ArtistType;
use App\Repository\ArtistRepository;



use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Endroid\QrCode\Color\ColorInterface;
use Endroid\QrCode\Color\Rgb;


use Endroid\QrCode\Color\Color;

use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;


#[Route('/artist')]
class ArtistController extends AbstractController
{
    #[Route('/', name: 'app_artist_index', methods: ['GET'])]
    public function index(ArtistRepository $artistRepository): Response
    {
        return $this->render('artist/index.html.twig', [
            'artists' => $artistRepository->findAll(),
        ]);
    }
    #[Route('/artistBack', name: 'app_artist_back_index', methods: ['GET'])]
    public function indexBack(ArtistRepository $artistRepository): Response
    {
        return $this->render('artist/indexBack.html.twig', [
            'artists' => $artistRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_artist_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $artist = new Artist();
        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($artist);
            $entityManager->flush();

            return $this->redirectToRoute('app_artist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('artist/new.html.twig', [
            'artist' => $artist,
            'form' => $form,
        ]);
    }
    #[Route('/newBack', name: 'app_artist_new_back', methods: ['GET', 'POST'])]
    public function newBack(Request $request, EntityManagerInterface $entityManager): Response
    {
        $artist = new Artist();
        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('imageArtist')->getData();

            if ($imageFile) {

                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('artist_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                }

                $artist->setImageArtist($newFilename);
            }

            $entityManager->persist($artist);
            $entityManager->flush();

            return $this->redirectToRoute('app_artist_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('artist/newBack.html.twig', [
            'artist' => $artist,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_artist_show', methods: ['GET'])]
    public function show(Artist $artist): Response
    {
        return $this->render('artist/show.html.twig', [
            'artist' => $artist,
        ]);
    }
    #[Route('/back/{id}', name: 'app_artist_show_back', methods: ['GET'])]
    public function showBack(Artist $artist): Response
    {
        return $this->render('artist/showBack.html.twig', [
            'artist' => $artist,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_artist_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Artist $artist, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_artist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('artist/edit.html.twig', [
            'artist' => $artist,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit/back', name: 'app_artist_edit_back', methods: ['GET', 'POST'])]
    public function editBack(Request $request, Artist $artist, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageArtist')->getData();

            if ($imageFile) {

                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('artist_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                }

                $artist->setImageArtist($newFilename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_artist_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('artist/editBack.html.twig', [
            'artist' => $artist,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_artist_delete', methods: ['POST'])]
    public function delete(Request $request, Artist $artist, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$artist->getId(), $request->request->get('_token'))) {
            $entityManager->remove($artist);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_artist_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/back', name: 'app_artist_delete_back', methods: ['POST'])]
    public function deleteBack(Request $request, Artist $artist, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$artist->getId(), $request->request->get('_token'))) {
            $entityManager->remove($artist);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_artist_back_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/artistBack/export', name: 'exporterExcel')]
    public function exporterExcel(ArtistRepository $artisteRepository): Response
    {
        // Récupérer tous les artistes depuis la base de données
        $artistes = $artisteRepository->findAll();

        // Créer un nouveau classeur Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Ajouter les en-têtes dans la première ligne
        $sheet->setCellValue('A1', 'Nom');
        
        $sheet->setCellValue('B1', 'Date de naissance');
        
        $sheet->setCellValue('D1', 'Nationalité');

        // Appliquer un style aux en-têtes de la première ligne
        $firstRowStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => [
                    'rgb' => 'F2F2F2',
                ],
            ],
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($firstRowStyle);

        // Itérer sur chaque artiste et ajouter les données dans les cellules de la feuille de calcul
        $row = 2;
        foreach ($artistes as $artiste) {
            $nom = $artiste->getNom();
            
            $dateNaissance = $artiste->getDateNaissance()->format('d/m/Y');
            
            $nationalite = $artiste->getNationalite();

            $sheet->setCellValue('A' . $row, $nom);
         
            $sheet->setCellValue('B' . $row, $dateNaissance);
           
            $sheet->setCellValue('D' . $row, $nationalite);

            $row++;
        }

        // Ajouter des bordures à toutes les cellules
        $allBordersStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => [
                        'rgb' => 'C2C2C2',
                    ],
                ],
            ],
        ];
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray($allBordersStyle);


// Ajuster la largeur des colonnes en fonction de leur contenu
        foreach (range('A', $highestColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

// Créer une réponse HTTP avec le contenu du fichier Excel exporté
        $response = new Response();
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="export_artistes.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        $writer = new Xlsx($spreadsheet);
        ob_start(); // démarrer la temporisation de sortie
        $writer->save('php://output');
        $content = ob_get_clean(); // récupérer la sortie générée dans une variable et arrêter la temporisation

        $response->setContent($content);

        return $response;

    }
    #[Route('{id}/qr-code', name: 'qr_code', methods: ['GET'])]
    public function artist_qr_code(Artist $artist)
{
    // Generate artist information string
    $artistInfo = 'Artist: ' . $artist->getNom().'bio: ' . $artist->getBiography().'nationalite: ' . $artist->getNationalite();

    // Create QR code
    $writer = new PngWriter();
    $qrCode = QrCode::create($artistInfo)
    ->setEncoding(new Encoding('UTF-8'))
    ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
    ->setSize(300)
    ->setMargin(10)
    ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
    ->setForegroundColor(new Color(0, 0, 0))
    ->setBackgroundColor(new Color(255, 255, 255));
    //$this->getParameter('artist_images_directory');
    $logo = Logo::create($this->getParameter('artist_images_directory').'/'.$artist->getImageArtist())
    ->setResizeToWidth(50)
    ->setPunchoutBackground(true)
;

// Create generic label
$label = Label::create($artist->getNom())
    ->setTextColor(new Color(255, 0, 0));

$result = $writer->write($qrCode, $logo, $label);
$result->saveToFile($this->getParameter('qr_images_directory').'/'.uniqid().'.png');

// Validate the result
$writer->validateResult($result, $artistInfo);
$result->saveToFile(__DIR__.'/qrcode.png');
$dataUri = $result->getDataUri();
return new Response($dataUri, Response::HTTP_OK);
}
}
