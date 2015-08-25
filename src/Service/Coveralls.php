<?php

namespace Kelunik\Chat\Integration\Service;

use Kelunik\Chat\Integration\Message;
use Kelunik\Chat\Integration\Service;

class Coveralls extends Service {
    public function handle(array $headers, $payload) {
        $author = $payload->committer_name;
        $coverageTotal = round($payload->covered_percent, 1);
        $coverageChange = round($payload->coverage_change, 1);
        $repository = $payload->repo_name;
        $url = $payload->url;

        $message = "%s %s [code coverage for `%s`](%s) by %s%% to %s%%.";
        $message = sprintf(
            $message,
            $author,
            $coverageChange > 0 ? "improved" : "worsened",
            $repository,
            $url,
            abs($coverageChange),
            $coverageTotal
        );

        return new Message($message, [
            "author" => $author,
            "change" => $coverageChange,
            "total" => $coverageTotal,
            "repository" => $repository,
            "url" => $url,
        ]);
    }
}
