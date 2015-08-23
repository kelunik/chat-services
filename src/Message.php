<?php

namespace Kelunik\Chat\Integration;

class Message {
    private $text;
    private $data;

    public function __construct(string $text, $data = null) {
        $this->text = $text;
        $this->data = $data;
    }

    public function getText(): string {
        return $this->text;
    }

    public function getData() {
        return $this->data;
    }
}