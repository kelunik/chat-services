<?php

namespace Kelunik\Chat\Integration\Service;

use Kelunik\Chat\Integration\Message;
use Kelunik\Chat\Integration\Service;

class Github extends Service {
    private $events = [
        "ping" => true,
    ];

    public function handle(array $headers, $payload) {
        $event = $this->getEventName($headers, $payload);

        if (isset($this->events[$event])) {
            return $this->$event($payload);
        } else {
            return null;
        }
    }

    public function ping($payload) {
        return new Message("_\"" . $payload->zen . "\"_");
    }

    public function getEventName(array $headers, $payload): string {
        list($header) = $headers["x-github-event"] ?? [""];

        return strtolower($header);
    }
}