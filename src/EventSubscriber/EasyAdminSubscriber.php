<?php

namespace App\EventSubscriber;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['setIllustration'],
            BeforeEntityUpdatedEvent::class => ['updateIllustration'],
        ];
    }

    public function updateIllustration(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();
        //dd($entity);
    }

    public function setIllustration(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();
        //dd($entity);
    }
}
