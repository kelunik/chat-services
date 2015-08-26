<?php

namespace Kelunik\Chat\Integration\Service;

use Kelunik\Chat\Integration\Message;
use Kelunik\Chat\Integration\Service;

class Github extends Service {
    private $events = [
        "ping" => true,
        "push" => true,
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

    public function push($payload) {
        $message = sprintf(
            "*[%s](%s) pushed %d new %s to [%s](%s).*",
            $payload->sender->login,
            $payload->sender->html_url,
            count($payload->commits),
            count($payload->commits) === 1 ? "commit" : "commits",
            $payload->repository->full_name,
            $payload->repository->html_url
        );

        return new Message($message, [
            "user" => $payload->sender->login,
            "user_url" => $payload->sender->html_url,
            "repository" => $payload->repository->full_name,
            "repository_url" => $payload->repository->html_url,
            "commits" => count($payload->commits),
        ]);
    }

    public function getEventName(array $headers, $payload): string {
        list($header) = $headers["x-github-event"] ?? [""];

        return strtolower($header);
    }
}