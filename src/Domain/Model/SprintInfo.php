<?php
declare(strict_types=1);

namespace Perform\PrivateProjects\SprintProgress\Domain\Model;

/**
 * Class SprintInfo Aggregate
 * @package Perform\PrivateProjects\SprintProgress\Domain\Model
 */
class SprintInfo
{
    private $sprintId;
    private $sprint;
    private $userStories;
    private $totalStoryPoints = 0;
    private $totalBurned = 0;

    public function __construct(int $sprintId, Sprint $sprint, UserStories $userStories)
    {
        $this->sprintId = $sprintId;
        $this->sprint = $sprint;
        $this->userStories = $userStories;
        foreach ($userStories->all() as $userStory) {
            $this->totalStoryPoints += $userStory->storyPoints();
            $this->totalBurned += $userStory->burnedStoryPoints();
        }
    }

    public function sprint(): Sprint
    {
        return $this->sprint;
    }

    public function sprintId(): int
    {
        return $this->sprintId;
    }

    public function userStories(): UserStories
    {
        return $this->userStories;
    }
}
