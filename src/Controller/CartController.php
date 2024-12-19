<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{


    private $pr;
    public function __construct(private readonly ProductRepository $productRepository){

        $this->pr;
            
    }


    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
public function index(SessionInterface $session): Response
{
    // Retrieve the cart from the session, defaulting to an empty array
    $cart = $session->get('cart', []);
    $cartWithData = [];

    // Loop through each cart item and retrieve the product data
    foreach ($cart as $id => $quantity) {
        $product = $this->productRepository->find($id); // Use the correct variable $id, not '$id'
        
        if ($product) {
            // Add the product data and quantity to the cartWithData array
            $cartWithData[] = [
                'product' => $product,
                'quantity' => $quantity
            ];
        }
    }

    // Calculate the total price of all items in the cart
    $total = array_sum(array_map(function ($item) {
        return $item['product']->getPrice() * $item['quantity'];
    }, $cartWithData));
    //dd($total);

    // Render the cart page with the items and total price
    return $this->render('cart/index.html.twig', [
        'items' => $cartWithData,
        'total' => $total
    ]);
}


    #[Route('/cart/add/{id}/', name: 'app_cart_new', methods: ['GET'])]
    public function AddToCard(int $id, SessionInterface $session): Response
    {
        // Ensure the session cart is always initialized as an array
        $cart = $session->get('cart', []);
    
        if (!is_array($cart)) {
            $cart = []; // Reinitialize if $cart is not an array
        }
    
        // Update the quantity or add the product to the cart
        if (isset($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }
    
        // Save the cart back to the session
        $session->set('cart', $cart);
    
        // Redirect to the cart page or another route
        return $this->redirectToRoute('app_cart');
    }
    #[Route('/cart/remove/{id}', name: 'app_cart_remove', methods: ['GET'])]
public function removeFromCart(int $id, SessionInterface $session): Response
{
    // Retrieve the cart from the session
    $cart = $session->get('cart', []);

    // Check if the product exists in the cart and remove it
    if (isset($cart[$id])) {
        unset($cart[$id]); // Remove the product with the given id from the cart
    }

    // Update the cart in the session
    $session->set('cart', $cart);

    // Redirect back to the cart page
    return $this->redirectToRoute('app_cart');
}
#[Route('/cart/clear', name: 'app_cart_clear', methods: ['GET'])]
public function clearCart(SessionInterface $session):Response
{
    // Clear the cart stored in the session
    $session->remove('cart');

    // Optionally, you can add a flash message to inform the user that the cart is cleared
    $this->addFlash('success', 'Your cart has been emptied.');

    // Redirect back to the cart page
    return $this->redirectToRoute('app_cart');
}


}
