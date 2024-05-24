<?php

namespace App\Controller;

use App\HttpRequest\Base;
use App\HttpRequest\SendRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    public function __construct(private readonly SendRequest $sendRequest)
    {
    }

    #[Route('/', name: 'app_product', methods: ['GET'])]
    public function index(): Response
    {
        $response = $this->sendRequest->send(Base::findProducts);

        if($response->getStatusCode() === Response::HTTP_OK) {
            $content = json_decode($response->getContent(), true);
        }

        return $this->render('product/index.html.twig', [
            'products' => $content['products'],
        ]);
    }

    #[Route('/create', name: 'app_product_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        if($request->getMethod() === Request::METHOD_GET) {
            return $this->render('product/create.html.twig');
        }


    }
}
