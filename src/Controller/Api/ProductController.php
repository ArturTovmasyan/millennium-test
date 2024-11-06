<?php

namespace App\Controller\Api;

use App\Entity\Product;
use App\Service\ValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/product')]
class ProductController extends AbstractController
{
    #[Route('/add', name: 'api_products_add', methods: ['POST'])]
    public function setProductsAction(Request $request, EntityManagerInterface $em, ValidatorService $validator): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $products = $content['products'] ?? [];

        if (empty($products)) {
            return $this->json(['errors' => 'Products array is empty'], Response::HTTP_BAD_REQUEST);
        }

        $errors = [];
        $isValid = true;

        $em->beginTransaction();

        try {
            foreach ($products as $key => $item) {
                $product = new Product();
                $product->setTitle($item['title'] ?? '');
                $product->setPrice($item['price'] ?? 0);

                // Validate product and collect errors
                $validationErrors = $validator->validate($product);

                if ($validationErrors) {
                    $errors[$key] = $validationErrors;
                    $isValid = false;
                } else {
                    $em->persist($product);
                }
            }

            if ($isValid) {
                $em->flush();
                $em->commit();
                return $this->json([], Response::HTTP_CREATED);
            }

            $em->rollback();
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);

        } catch (\Exception $e) {
            $em->rollback();
            throw $e;
        }
    }
}
