<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\UserOrder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/user-orders')]
class UserOrderController extends AbstractController
{
    #[Route('/{id}', name: 'api_user_orders_get', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getUserOrdersAction(int $id, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException("User with ID $id not found");
        }

        $orders = $em->getRepository(UserOrder::class)->findOrdersByUser($id);

        $result = [
            'fullName' => $user->getFullName(),
            'orders' => $orders
        ];

        return $this->json($result)->setEncodingOptions(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
