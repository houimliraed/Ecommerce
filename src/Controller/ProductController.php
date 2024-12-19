<?php

namespace App\Controller;

use App\Entity\AddProductHistory;
use App\Entity\Product;
use App\Form\AddProductHistoryType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('editor/product')]
final class ProductController extends AbstractController
{
    #[Route(name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager ): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData(); // Get the image from the form
            if ($image) {
                // Create a unique file name based on the original name and the file extension
                $newFileName = uniqid() . '.' . $image->guessExtension();
            
                try {
                    // Move the file to the directory where it should be stored
                    $image->move(
                        $this->getParameter('image_dir'), // Directory parameter defined in services.yaml
                        $newFileName
                    );
            
                    // Set the new file name to the product
                    $product->setImage($newFileName);
            
                } catch (FileException $exception) {
                    // Handle the exception (e.g., log the error or display a flash message)
                    $this->addFlash('error', 'File upload failed.');
                }
            $entityManager->persist($product);
            $entityManager->flush();

            $stockHistory = new AddProductHistory();
            $stockHistory->setQte($product->getStock());
            $stockHistory->setProduct($product);
            $stockHistory->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($stockHistory);
            $entityManager->flush();


            $this->addFlash('success','thank you , your product added !');
            }

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success','your product has been modified !');

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $this->addFlash('danger','your product is being deleted !');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/add/product/{id}/stock', name: 'app_product_stock_add', methods: ['GET', 'POST'])]
    public function addStock($id,EntityManagerInterface $entityManager,Request $request,ProductRepository $productRepository):Response{

        $addStock =new AddProductHistory;
        $form=$this->createForm(AddProductHistoryType::class,$addStock);
        $form->handleRequest($request);

        $product = $productRepository->find($id);
        if($form->isSubmitted()&&$form->isValid())
        {
            if($addStock->getQte()>0){
            $newQte = $product->getStock() + $addStock->getQte();
            $product->setStock($newQte);

            $addStock->setCreatedAt(new \DateTimeImmutable());
            $addStock->setProduct($product);

            $entityManager->persist($addStock);
            $entityManager->flush();

            $this->addFlash('success',"your product stock has been modified!");

            return $this->redirectToRoute('app_product_index');
        }else{
            $this->addFlash('danger','stock has not been updated !, it has to be > 0');
            $this->redirectToRoute('app_product_stock_add',['id'=>$product->getId()]);
        }


        }

        return $this->render('product/addStock.html.twig',[
            'form'=> $form->createView(),
            'product'=> $product
        ]
        );
    }

}
