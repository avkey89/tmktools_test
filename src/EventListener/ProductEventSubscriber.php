<?php

namespace App\EventListener;


use App\Entity\Product;
use App\Services\MailerServices;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class ProductEventSubscriber implements EventSubscriber
{
    private $mailerServices;

    public function __construct(MailerServices $mailerServices)
    {
        $this->mailerServices = $mailerServices;
    }

    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
            'postUpdate',
        );
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->index($args, 'update');
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->index($args, 'add');
    }

    public function index(LifecycleEventArgs $args, $type)
    {
        $entity = $args->getObject();

        if ($entity instanceof Product) {
            $subject = ($type == 'add' ? 'Product added (subscriber)' : 'Product updated (subscriber)');
            $message = "Information about product: ".json_encode($entity->toArray());
            $this->mailerServices->sendMail($subject, $message);
        }
    }
}