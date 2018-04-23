<?php
declare(strict_types=1);

namespace Perform\PrivateProjects\SprintProgress\Domain\Model;

/**
 * Class UserStory Entity
 * @package Perform\PrivateProjects\SprintProgress\Domain\Model
 */
class UserStory
{
    private $id;
    private $name;
    private $storyPoints;
    private $subTaskCount;
    private $subTaskDoneCount;
    private $burnedStoryPoints;

    public function __construct(
        int $id,
        string $name,
        int $storyPoints,
        int $subTaskCount,
        int $subTaskDoneCount
    ) {
        $this->id = $id;
        $this->name = $name;
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
