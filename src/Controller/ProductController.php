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
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ProductController extends AbstractController
{
    public function __construct(
        private readonly SendRequest $sendRequest,
        private readonly CacheInterface $cache
    )
    {
    }

    private function getItemsFromCache(): array
    {
//        return $this->cache->get(
//            'products',
//            function (ItemInterface $item){
//                $item->expiresAfter(new \DateInterval('PT1S'));
//                $response = $this->sendRequest->send(Base::FIND_PRODUCTS);
//
//                if($response->getStatusCode() === Response::HTTP_OK) {
//                    return $response->toArray();
//                }
//
//                throw new \Exception('Falha ao buscar itens!');
//            }
//        );

        $response = $this->sendRequest->send(Base::FIND_PRODUCTS);

        if($response->getStatusCode() === Response::HTTP_OK) {
            return $response->toArray();
        }

        throw new \Exception('Falha ao buscar itens!');
    }

    /**
     * @throws \Exception
     */
    private function getProductDTO(int $productId): ProductDTO
    {
        $products = $this->getItemsFromCache();

        $ids = array_column($products['products'], 'id');
        if(($position = array_search($productId, $ids)) !== false){
            $product = $products['products'][$position];
        } else {
            throw new \Exception('Produto nao encontrado!');
        }

        return new ProductDTO(
            $product['id'],
            $product['name'],
            $product['price'],
            $product['quantity']
        );
    }

    /**
     * @throws \Exception
     */
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(): Response
    {
        try{
            $content = $this->getItemsFromCache();

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
                return $this->render('product/productForm.html.twig', compact(['productForm']));
            }

            $productForm->handleRequest($request);

            if($productForm->isSubmitted() && $productForm->isValid()) {
                $response = $this->sendRequest->send(Base::CREATE_PRODUCT, $productDTO);

                if($response->getStatusCode() === Response::HTTP_CREATED) {
                    $this->addFlash('success', 'Produto criado com sucesso!');
                    $this->cache->delete('products');
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

    #[Route('/edit/{productId}', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $productId): Response
    {
        try {
            $productDTO = $this->getProductDTO($productId);
            $productForm = $this->createForm(ProductType::class, $productDTO, ['edit' => true]);

            if($request->getMethod() === Request::METHOD_GET) {
                return $this->render('product/productForm.html.twig', compact(['productForm']));
            }

            $productForm->handleRequest($request);

            if($productForm->isSubmitted() && $productForm->isValid()) {
                $response = $this->sendRequest->send(Base::UPDATE_PRODUCT, $productDTO);

                if($response->getStatusCode() === Response::HTTP_OK) {
                    $this->addFlash('success', 'Produto atualizado com sucesso!');
                    $this->cache->delete('products');
                    return $this->redirectToRoute('app_product_index');
                }

                $content = $response->toArray();
                $message = !empty($content['message']) ? $content['message'] : 'Erro ao editar o produto!';
                $this->addFlash('danger', $message);
                return $this->redirectToRoute('app_product_edit', ['productId' => $productId]);
            }

            $errorForm = $productForm->getErrors(true);

            if(count($errorForm) > 0){
                $error = (string) $errorForm;
            } else {
                $error = 'Nao foi possivel gravar a informacao!';
            }

            $this->addFlash('danger', $error);
            return $this->redirectToRoute('app_product_edit', ['productId' => $productId]);
        }catch (\Exception $e){
            throw $e;
        }
    }

    #[Route('/remove/{productId}', name: 'app_product_remove', methods: ['GET'])]
    public function remove(Request $request, int $productId): Response
    {
        try{
            $productDTO = $this->getProductDTO($productId);
            $response = $this->sendRequest->send(Base::DELETE_PRODUCT, $productDTO);

            if($response->getStatusCode() === Response::HTTP_OK) {
                $this->addFlash('success', 'Produto removido com sucesso!');
                $this->cache->delete('products');
                return $this->redirectToRoute('app_product_index');
            }

            $content = $response->toArray();
            $message = !empty($content['message']) ? $content['message'] : 'Erro ao remover o produto!';
            $this->addFlash('danger', $message);
            return $this->redirectToRoute('app_product_edit', ['productId' => $productId]);
        }catch (\Exception $e){
            throw $e;
        }
    }
}
