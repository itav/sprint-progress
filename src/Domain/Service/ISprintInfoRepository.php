<?php
declare(strict_types=1);

namespace Perform\PrivateProjects\SprintProgress\Domain\Service;

use Perform\PrivateProjects\SprintProgress\Domain\Model\SprintInfo;

interface ISprintInfoRepository
{
    public function getSprintInfo(): SprintInfo;
}
