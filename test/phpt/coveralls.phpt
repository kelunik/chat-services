--FILE--
<?php

require __DIR__ . "/../../vendor/autoload.php";

$service = new Kelunik\Chat\Integration\Service\Coveralls;
$message = $service->handle([], (object) [
    "badge_url" => "http://example.com/badge.png",
    "branch" => "master",
    "committer_name" => "John Doe",
    "repo_name" => "chat/services",
    "commit_sha" => "abcdef",
    "committer_email" => "me@example.com",
    "covered_percent" => 95.1,
    "commit_message" => "Fix #123",
    "coverage_change" => 1.5,
    "url" => "http://example.com/",
]);

print $message->getText();
print "\n";
print "\n";
print_r($message->getData());
--EXPECT--
John Doe improved [code coverage for `chat/services`](http://example.com/) by 1.5% to 95.1%.

Array
(
    [author] => John Doe
    [change] => 1.5
    [total] => 95.1
    [repository] => chat/services
    [url] => http://example.com/
)