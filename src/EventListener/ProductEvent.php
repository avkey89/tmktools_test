<?php

namespace App\EventListener;


use App\Entity\Product;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ProductEvent
{

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
        $entity = $args->getEntity();

        if ($entity instanceof Product) {
            $subject = ($type == 'add' ? 'Product added (observer)' : 'Product updated (observer)');
            $message = "Information about product: ".json_encode($entity->toArray());
            $this->mailerServices->sendMail($subject, $message);
        }
    }
}