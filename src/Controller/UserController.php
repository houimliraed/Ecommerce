<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }


    #[Route('/admin/user/{id}/to/editor', name: 'app_user_to_editor')]
    public function updateUserRole(EntityManagerInterface $entityManager, User $user): Response
    {
      $user->setRoles(["ROLE_EDITOR","ROLE_USER"]);
      $entityManager->flush();
      $this->addFlash('success','User updated role and added successfuly');
      

      return $this->redirectToRoute('app_user');
    }


    
    #[Route('/admin/user/{id}/remove', name: 'app_user_remove')]
    public function userRemove(EntityManagerInterface $entityManager,$id, UserRepository $userRepository): Response
    {
     $userfind = $userRepository->find($id);
     $entityManager->remove($userfind);
     $entityManager->flush();
      
     $this->addFlash('danger','User deleted !!');
      return $this->redirectToRoute('app_user');
    }
}
