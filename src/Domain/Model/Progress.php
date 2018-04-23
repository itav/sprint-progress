<?php
declare(strict_types=1);

namespace Perform\PrivateProjects\SprintProgress\Domain\Model;

/**
 * Class Progress - Value Object
 * @package Perform\PrivateProjects\SprintProgress\Domain\Model
 */
class Progress
{
    private $rabbitPosition;
    private $teamPosition;

    public function __construct(int $rabbitPosition, int $teamPosition)
    {
        $this->rabbitPosition = $rabbitPosition;
        $this->teamPosition = $teamPosition;
    }

    public function rabbitPosition(): int
    {
        return $this->rabbitPosition;
    }

    public function teamPosition(): int
    {
        return $this->teamPosition;
    }
}
