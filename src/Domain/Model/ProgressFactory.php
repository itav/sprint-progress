<?php
declare(strict_types=1);

namespace Perform\PrivateProjects\SprintProgress\Domain\Model;

class ProgressFactory
{
    public function create(int $rabbitPosition, int $teamPosition): Progress
    {
        $this->validate($rabbitPosition, $teamPosition);
        return new Progress($rabbitPosition, $teamPosition);
    }

    private function validate(int $rabbitPosition, int $teamPosition): void
    {
        if ($rabbitPosition < 0) {
            throw new \InvalidArgumentException('rabbit position can not be lower than 0');
        }

        if ($teamPosition < 0) {
            throw new \InvalidArgumentException('team position can not be lower than 0');
        }
    }
}
