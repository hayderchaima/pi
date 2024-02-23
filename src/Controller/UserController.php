<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Enum\Role;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/back', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, Security $security): Response
    {
        $user=$this->getUser();
        if ($user!==null) {
           $role = $user->getRole();

           if ($role === Role::ADMIN) {
            return $this->render('user/index.html.twig', [
                'users' => $userRepository->findAll(),
            ]);
           } else {
               // The user is a regular user
               // Do something for regular users
               return $this->redirectToRoute('app_user_index_front');
           }
        }
        
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    #[Route('/front', name: 'app_user_index_front', methods: ['GET'])]
    public function indexF(UserRepository $userRepository): Response
    {
        $user=$this->getUser();
         if ($user!==null) {
            $role = $user->getRole();

            if ($role !== Role::ADMIN) {
                return $this->render('user/indexfront.html.twig', [
                    'users' => $userRepository->findAll(),
                ]);
            } else {
                // The user is a regular user
                // Do something for regular users
                return $this->redirectToRoute('app_user_index');
            }
         }
        return $this->render('user/indexFront.html.twig', [
            
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
