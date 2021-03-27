<?php

namespace App\EventSubscriber;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['setIllustration'],
        ];
    }

    public function setIllustration(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();
        //dd($entity);
    }
}
