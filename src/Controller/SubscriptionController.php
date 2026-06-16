<?php

namespace App\Controller;

use App\Doctrine\TenantContext;
use App\Entity\Control\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    /**
     * @Route("/subscription/expired", name="subscription_expired")
     */
    public function expired(
        Request $request,
        TenantContext $tenantContext,
        EntityManagerInterface $controlEm
    ): Response {
        $reason = $request->query->get('reason', 'expired');
        $tenant = $tenantContext->hasTenant() ? $tenantContext->getTenant() : null;

        $subscription = null;
        if ($tenant !== null) {
            $subscription = $controlEm->getRepository(Subscription::class)
                ->findOneBy(['tenant' => $tenant], ['id' => 'DESC']);
        }

        return $this->render('subscription/expired.html.twig', [
            'reason' => $reason,
            'tenant' => $tenant,
            'subscription' => $subscription,
        ]);
    }
}
