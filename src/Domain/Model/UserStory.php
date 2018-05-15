<?php
declare(strict_types=1);

namespace Perform\PrivateProjects\SprintProgress\Domain\Model;

/**
 * Class UserStory Entity
 * @package Perform\PrivateProjects\SprintProgress\Domain\Model
 */
class UserStory
{
    const BASE_BROWSE_JIRA_URL = 'https://jira2.performgroup.com/browse/';

    private $id;
    private $name;
    private $key;
    private $link;
    private $storyPoints;
    private $subTaskCount;
    private $subTaskDoneCount;
    private $burnedStoryPoints;

    public function __construct(
        int $id,
        string $name,
        string $key,
        int $storyPoints,
        int $subTaskCount,
        int $subTaskDoneCount
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->key = $key;
        $this->link = self::BASE_BROWSE_JIRA_URL . $key;
        $this->storyPoints = $storyPoints;
        $this->subTaskCount = $subTaskCount;
        $this->subTaskDoneCount = $subTaskDoneCount;
        $this->burnedStoryPoints = $subTaskDoneCount * $storyPoints / $subTaskCount;
    }

    public function storyPoints(): int
    {
        return $this->storyPoints;
    }

    public function subTaskCount(): int
    {
        return $this->subTaskCount;
    }

    public function subTaskDoneCount(): int
    {
        return $this->subTaskDoneCount;
    }

    public function burnedStoryPoints(): float
    {
        return $this->burnedStoryPoints;
    }
}
