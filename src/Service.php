<?php

namespace Kelunik\Chat\Integration;

use Amp\Artax\Client;
use Amp\Artax\Request;
use Amp\Promise;
use JsonSchema\Validator;
use ReflectionClass;
use stdClass;

abstract class Service {
    private $name;
    private $validator;
    private $schema;
    private $http;

    /**
     * @param Validator $validator
     * @param Client $http
     */
    public function __construct (Validator $validator, Client $http) {
        $this->validator = $validator;
        $this->http = $http;
        $this->schema = [];
    }

    /**
     * Handles a payload and submits messages to chat rooms as appropriate.
     *
     * @param int $roomId
     * @param array $header
     * @param array $payload
     * @return mixed
     */
    abstract public function handle (int $roomId, array $header, array $payload);

    /**
     * Adds a JSON schema to validate against.
     *
     * @param string $event
     * @param stdClass $schema
     */
    public function addSchema (string $event, stdClass $schema) {
        $this->schema[$event] = $schema;
    }

    /**
     * Checks if a given payload is valid and safe to "handle".
     *
     * @param array $headers
     * @param array $payload
     * @return bool
     */
    public function isValid (array $headers, array $payload): bool {
        $event = $this->getEvent($headers);

        if (!isset($this->schema[$event])) {
            return false;
        }

        $this->validator->check($payload, $this->schema[$event]);
        $valid = $this->validator->isValid();
        $this->validator->reset();

        return $valid;
    }

    /**
     * Submits a message through the API.
     *
     * @param int $roomId
     * @param string $text
     * @param string $icon
     * @return Promise
     */
    public function submitMessage (int $roomId, string $text, string $icon): Promise {
        $request = (new Request)
            ->setUri("http://api.localhost:3032/messages")
            ->setMethod("PUT")
            ->setHeader("content-type", "application/json")
            ->setBody(json_encode([
                "room_id" => $roomId,
                "text" => $text,
                "type" => "status",
                "data" => $icon ? $this->getName() . "/" . $icon : null,
            ]));

        return $this->http->request($request);
    }

    protected function getEvent (array $headers) {
        return $this->getName();
    }

    protected function getName () {
        if (!$this->name) {
            $name = (new ReflectionClass($this))->getShortName();
            $name = preg_replace("~([A-Z])~", "-\\1", $name);
            $name = strtolower($name);

            $this->name = $name;
        }

        return $this->name;
    }
}