<?php

namespace App\Controller;

use App\Entity\AddProductHistory;
use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class CategoryController extends AbstractController
{
    #[Route('/admin/category', name: 'app_category')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }
    #[Route('/admin/category/add', name: 'app_category_add')]
    public function addCategory(EntityManagerInterface $entityManager , Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class,$category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($category);
            $entityManager->flush();

            

            $this->addFlash('success','votre catégorie a été crée');
            return $this->redirectToRoute('app_category');
        }


        return $this->render('category/newCategory.html.twig',['form'=> $form->createView()]);

    }


    #[Route('/admin/category/{id}/update', name: 'app_category_update')]
    
public function updateCategory(Category $category,EntityManagerInterface $entityManager,Request $request):Response{
    
    $form = $this->createForm(CategoryFormType::class,$category);
    $form->handleRequest($request);

    if($form->isSubmitted()&&$form->isValid()){
        $entityManager->flush();

        $this->addFlash('success','votre catégorie a été modifié');

        return $this->redirectToRoute('app_category');

        
    }


    return $this->render('category/updateCategory.html.twig',['form'=> $form->createView()]);

}

#[Route('/admin/category/{id}/delete', name: 'app_category_delete')]
    
public function deleteCategory(Category $category,EntityManagerInterface $entityManager):Response{
    
$entityManager->remove($category);
$entityManager->flush();

$this->addFlash('danger','votre catégorie a été supprimé');
return $this->redirectToRoute('app_category');

}

}
