<?php

namespace App\Controller\Api;

use App\Entity\Product;
use App\Service\ValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/product')]
class ProductController extends AbstractController
{
    #[Route('/add', name: 'api_products_add', methods: ['POST'])]
    public function setProductsAction(Request $request, EntityManagerInterface $em, ValidatorService $validator): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $products = $content['products'] ?? [];

        if (empty($products)) {
            return $this->json(['errors' => 'Products is empty'], Response::HTTP_BAD_REQUEST);
        }

        $em->beginTransaction();

        try {
            $isValid = true;
            $errors = [];

            foreach ($products as $key => $item) {
                $product = (new Product())
                    ->setTitle($item['title'] ?? '')
                    ->setPrice($item['price'] ?? 0);

                $validationErrors = $validator->validate($product);

                if ($validationErrors) {
                    $errors[$key] = $validationErrors;
                    $isValid = false;
                    continue;
                }

                $em->persist($product);
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
