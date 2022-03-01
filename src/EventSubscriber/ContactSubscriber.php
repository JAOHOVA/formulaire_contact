<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use \App\Event\ContactSentEvent;

class ContactSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onContactSentEvent(ContactSentEvent $event)
    {
        $contact = $event->getContact();

        $this->logger->info(sprintf('%s contacted you with message "%s"', $contact->getEmail(), $contact->getMessage()));
    }

    public static function getSubscribedEvents()
    {
        return [
            ContactSentEvent::class => 'onContactSentEvent',
        ];
    }
}