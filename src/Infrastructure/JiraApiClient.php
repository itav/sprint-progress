<?php
declare(strict_types = 1);

namespace Perform\PrivateProjects\SprintProgress\Infrastructure;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class JiraApiClient
{
    const LOGIN = 'sylwester.kowalski';
    const PASSWORD = '';
    const HOST = 'https://jira2.performgroup.com';

    const GET_SPRINT_URI = '/rest/agile/1.0/board/%d/sprint?state=%s';
    const GET_ISSUES_URI = '/rest/agile/1.0/board/%d/sprint/%d/issue?startAt=%d&maxResults=%d';
    const GET_ISSUE_ESTIMATION = '/rest/agile/1.0/issue/%d';

    const SPRINT_TYPE_ACTIVE = 'active';
    const WEB_CLIENT_BOARD_ID = 68;

    private $httpClient;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getSprintByType(int $boardId, string $type): array
    {
        $uri = sprintf(self::GET_SPRINT_URI, $boardId, $type);
        $ret = $this->sendRequest($uri);
        $data = json_decode($ret->getBody()->getContents(), true);
        return $data['values'][0] ?? [];
    }

    public function getIssuesBySprint(int $sprintId, int $boardId): array
    {
        $startAt = 0;
        $max = 100;
        $uri = sprintf(self::GET_ISSUES_URI, $boardId, $sprintId, $startAt, $max);
        $ret = $this->sendRequest($uri);
        $data = json_decode($ret->getBody()->getContents(), true);
        $max = $data['maxResults'] ?? 100;
        $total = $data['total'] ?? 0;
        $result = $data['issues'] ?? [];
        while (count($result) < $total) {
            $startAt += $max;
            $uri = sprintf(self::GET_ISSUES_URI, $boardId, $sprintId, $startAt, $max);
            $ret = $this->sendRequest($uri);
            $next = json_decode($ret->getBody()->getContents(), true);
            $result = array_merge($result, $next['issues'] ?? []);
        }
        return $result;
    }

    private function sendRequest(string $uri): ResponseInterface
    {
        return $this->httpClient->get(self::HOST . $uri, [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode(self::LOGIN . ':' . self::PASSWORD),
                'Content-Type' => 'application/json',
            ]
        ]);
    }
}
