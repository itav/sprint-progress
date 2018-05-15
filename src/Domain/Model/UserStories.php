<?php
declare(strict_types=1);

namespace Perform\PrivateProjects\SprintProgress\Domain\Model;

class UserStories
{
    private $items = [];

    public function add(UserStory $story): void
    {
        $this->items[] = $story;
    }

    /**
     * @return UserStory[]
     */
    public function all(): array
    {
        return $this->items;
    }
}
