<?php
declare(strict_types=1);

namespace Perform\PrivateProjects\SprintProgress\Infrastructure;

use Perform\PrivateProjects\SprintProgress\Domain\Model\SprintFactory;
use Perform\PrivateProjects\SprintProgress\Domain\Model\SprintInfo;
use Perform\PrivateProjects\SprintProgress\Domain\Model\UserStoryFactory;
use Perform\PrivateProjects\SprintProgress\Domain\Service\ISprintInfoRepository;

class SprintInfoRepository implements ISprintInfoRepository
{
    private $jiraApiClient;
    private $sprintFactory;
    private $userStoryFactory;

    public function __construct(
        JiraApiClient $jiraApiClient,
        SprintFactory $sprintFactory,
        UserStoryFactory $userStoryFactory
    ) {
        $this->jiraApiClient = $jiraApiClient;
        $this->sprintFactory = $sprintFactory;
        $this->userStoryFactory = $userStoryFactory;
    }

    public function getSprintInfo(): SprintInfo
    {
        $boardId = JiraApiClient::WEB_CLIENT_BOARD_ID;
        $data = $this->jiraApiClient->getSprintByType($boardId, JiraApiClient::SPRINT_TYPE_ACTIVE);
        $sprint = $this->sprintFactory->createFromArray($data);
        $items = $this->jiraApiClient->getIssuesBySprint($sprint->id(), $boardId);
        $userStories = $this->userStoryFactory->createFromArrays($items);
        return new SprintInfo($sprint->id(), $sprint, $userStories);
    }
}
