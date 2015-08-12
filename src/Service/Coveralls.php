<?php

namespace Kelunik\Chat\Integration\Service;

use Amp\Promise;
use Kelunik\Chat\Integration\Service;

class Coveralls extends Service {
    public function handle (int $roomId, array $headers, array $payload): Promise {
        $author = $payload["committer_name"];
        $coverageTotal = round((float) $payload["coverage_percent"], 1);
        $coverageChange = round((float) $payload["coverage_change"], 1);
        $repository = $payload["repo_name"];
        $url = $payload["url"];

        if (abs($coverageChange) < 0.001) {
            return "Aborting... no coverage change!";
        }

        $message = "%s %s [code coverage for `%s`](%s) by %f% to %f%.";
        $message = sprintf(
            $message,
            $author,
            $coverageChange < 0 ? "improved" : "worsened",
            $repository,
            $url,
            abs($coverageChange),
            $coverageTotal
        );

        $icon = $coverageChange < 0 ? "std_green" : "std_red";
        return $this->submitMessage($roomId, $message, $icon);
    }
}
