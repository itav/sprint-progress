<?php
declare(strict_types=1);

namespace Perform\PrivateProjects\SprintProgress\Domain\Model;


class SprintFactory
{
    public function create(int $id, string $name, \DateTime $start, \DateTime $stop): Sprint
    {
        $this->validate(...func_get_args());
        return new Sprint($id, $name, $start, $stop);
    }

    public function createFromArray(array $data): Sprint
    {
        $id = (int)($data['id'] ?? -1);
        $name = (string)($data['name'] ?? '');
        $startDate = (string)($data['startDate'] ?? '');
        $endDate = (string)($data['endDate'] ?? '');
        return $this->create($id, $name, new \DateTime($startDate), new \DateTime($endDate));
    }

    private function validate(int $id, string $name, \DateTime $start, \DateTime $stop): void
    {
    }
}