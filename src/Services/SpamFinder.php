<?php
// src/Service/SpamFinder.php

namespace App\Services;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SpamFinder {
    private $logger;
    private $requestStack;
    private $spamValues = ["aaaaa", "sdfsdf"];

    public function __construct(LoggerInterface $logger, RequestStack $requestStack)
    {
        $this->logger = $logger;
        $this->requestStack = $requestStack;
    }

    public function isSpam(string $text): bool
    {
        $ip = $this->requestStack->getCurrentRequest()->getClientIp();

        foreach ($this->spamValues as $spamValue) {
            if (str_contains($text, $spamValue)) {
                $this->logger->info("Spam detected from IP: " . $ip, ['spam_text' => $text]);
                return true;
            }
        }

        return false;
    }
}
