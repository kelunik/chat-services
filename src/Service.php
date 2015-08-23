<?php

namespace Kelunik\Chat\Integration;

use ReflectionClass;

abstract class Service {
    private $name;

    /**
     * Handles a payload and returns a messages to send.
     *
     * @param array $headers
     * @param mixed $payload
     * @return mixed
     */
    abstract public function handle(array $headers, $payload);

    /**
     * Returns the name of the event for verifying payloads or the service's name if there's only one event for that
     * service.
     *
     * @param array $headers
     * @param mixed $payload
     * @return string
     */
    public function getEventName(array $headers, $payload): string {
        return $this->getServiceName();
    }

    /**
     * Returns the name of this service in a lowercase and dash-splitted version.
     *
     * @return string
     */
    public final function getServiceName(): string {
        if (!$this->name) {
            $name = (new ReflectionClass($this))->getShortName();
            $name = preg_replace("~([A-Z])~", "-\\1", $name);
            $name = strtolower($name);

            $this->name = $name;
        }

        return $this->name;
    }
}
