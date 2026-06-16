<?php

namespace App\EventListener;

use App\Doctrine\TenantContext;
use App\Entity\Control\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SubscriptionGateListener
{
    private const GRACE_DAYS = 3;

    private const ALLOWED_PATHS = [
        '/subscription/expired',
        '/login',
        '/login_check',
        '/logout',
    ];

    public function __construct(
        private readonly TenantContext $tenantContext,
        private readonly EntityManagerInterface $controlEm,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if (!$this->tenantContext->hasTenant()) {
            return;
        }

        $path = $event->getRequest()->getPathInfo();
        foreach (self::ALLOWED_PATHS as $allowed) {
            if (str_starts_with($path, $allowed)) {
                return;
            }
        }

        $tenant = $this->tenantContext->getTenant();
        $subscription = $this->controlEm->getRepository(Subscription::class)
            ->findOneBy(['tenant' => $tenant], ['id' => 'DESC']);

        if ($subscription === null) {
            return;
        }

        $now = new \DateTimeImmutable();

        switch ($subscription->getStatus()) {
            case Subscription::STATUS_ACTIVE:
                $end = $subscription->getCurrentPeriodEnd();
                if ($end !== null && $end < $now) {
                    $this->block($event, 'expired');
                }
                break;

            case Subscription::STATUS_TRIAL:
                $trialEnd = $subscription->getTrialEndsAt();
                if ($trialEnd !== null && $trialEnd < $now) {
                    $this->block($event, 'trial_expired');
                }
                break;

            case Subscription::STATUS_PAST_DUE:
                $end = $subscription->getCurrentPeriodEnd();
                $grace = $end?->modify('+' . self::GRACE_DAYS . ' days');
                if ($grace !== null && $grace < $now) {
                    $this->block($event, 'past_due');
                }
                break;

            case Subscription::STATUS_CANCELED:
                $this->block($event, 'canceled');
                break;
        }
    }

    private function block(RequestEvent $event, string $reason): void
    {
        $url = $this->urlGenerator->generate('subscription_expired', ['reason' => $reason]);
        $event->setResponse(new RedirectResponse($url));
    }
}
