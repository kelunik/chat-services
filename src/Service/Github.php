<?php

namespace Kelunik\Chat\Integration\Service;

use Amp\Promise;
use Amp\Success;
use Kelunik\Chat\Integration\Service;
use function Amp\coroutine;

class Github extends Service {
    private $events = [
        "ping" => true,
    ];

    public function handle (int $roomId, array $headers, $payload): Promise {
        $event = $this->getEvent($headers);

        if (isset($this->events[$event])) {
            return coroutine([$this, $event]);
        } else {
            return new Success("no action");
        }
    }

    public function ping (int $roomId, $payload) {
        return $this->submitMessage($roomId, "_\"" . $payload->zen . "\"_", "github");
    }

    protected function getEvent (array $headers) {
        list($header) = $headers["x-github-event"] ?? [""];
        return strtolower($header);
    }
}