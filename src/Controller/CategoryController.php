<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }
    #[Route('/category/add', name: 'app_category_add')]
    public function addCategory(EntityManagerInterface $entityManaer , Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class,$category);

        $form->handleRequest($request);


        return $this->render('category/newCategory.html.twig',['form'=> $form->createView()]);

    }
}
