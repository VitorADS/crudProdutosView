<?php

namespace App\Controller;

use App\DTO\ProductDTO;
use App\Form\ProductType;
use App\HttpRequest\Base;
use App\HttpRequest\SendRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ProductController extends AbstractController
{
    public function __construct(private readonly SendRequest $sendRequest)
    {
    }

    /**
     * @throws \Exception
     */
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(): Response
    {
        try{
            $response = $this->sendRequest->send(Base::findProducts);

            if($response->getStatusCode() === Response::HTTP_OK) {
                $content = $response->toArray();
            }

            return $this->render('product/index.html.twig', [
                'products' => $content['products'],
            ]);
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    #[Route('/create', name: 'app_product_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        try {
            $productDTO = new ProductDTO();
            $productForm = $this->createForm(ProductType::class, $productDTO);

            if($request->getMethod() === Request::METHOD_GET) {
                return $this->render('product/create.html.twig', compact(['productForm']));
            }

            $productForm->handleRequest($request);

            if($productForm->isSubmitted() && $productForm->isValid()) {
                $response = $this->sendRequest->send(Base::createProduct, $productDTO);

                if($response->getStatusCode() === Response::HTTP_OK) {
                    $this->addFlash('success', 'Produto criado com sucesso!');
                    return $this->redirectToRoute('app_product_index');
                }

                $content = $response->toArray();
                $message = !empty($content['message']) ? $content['message'] : 'Erro ao cadastrar produto!';
                $this->addFlash('danger', $message);
                return $this->redirectToRoute('app_product_create');
            }

            $errorForm = $productForm->getErrors(true);

            if(count($errorForm) > 0){
                $error = (string) $errorForm;
            } else {
                $error = 'Nao foi possivel gravar a informacao!';
            }

            $this->addFlash('danger', $error);
            return $this->redirectToRoute('app_product_create');
        }catch (\Exception $e){
            throw $e;
        }
    }
}
