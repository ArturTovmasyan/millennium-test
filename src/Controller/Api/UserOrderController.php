<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\UserOrder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/user-orders')]
class UserOrderController extends AbstractController
{
    #[Route('/{id}', name: 'api_user_orders_get', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getUserOrdersAction(int $id, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            throw new NotFoundHttpException("User with ID $id not found");
        }

        $orders = $em->getRepository(UserOrder::class)->findOrdersByUser($id);

        $result = [
            'fullName' => $user->getFullName(),
            'orders' => $orders
        ];

        return $this->json($result);
    }
}
