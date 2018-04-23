<?php
declare(strict_types=1);

namespace Perform\PrivateProjects\SprintProgress\Domain\Model;

/**
 * Class Sprint Entity
 * @package Perform\PrivateProjects\SprintProgress\Domain\Model
 */
class Sprint
{
    private $id;
    private $name;
    private $startDate;
    private $endDate;

    public function __construct(int $id, string $name, \DateTime $startDate, \DateTime $endDate)
    {
        $this->id = $id;
        $this->name = $name;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function startDate(): \DateTime
    {
        return $this->startDate;
    }

    public function endDate(): \DateTime
    {
        return $this->endDate;
    }

    public function id(): int
    {
        return $this->id;
    }
}
