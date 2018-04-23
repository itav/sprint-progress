<?php
declare(strict_types=1);

namespace Perform\PrivateProjects\SprintProgress\Application;

use Perform\PrivateProjects\SprintProgress\Domain\Model\ProgressCalculator;
use Perform\PrivateProjects\SprintProgress\Domain\Service\ISprintInfoRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class Controller
{
    private $progressCalculator;
    private $sprintInfoRepository;

    public function __construct(
        ProgressCalculator $progressCalculator,
        ISprintInfoRepository $sprintInfoRepository
    ) {
        $this->sprintInfoRepository = $sprintInfoRepository;
        $this->progressCalculator = $progressCalculator;
    }

    public function getProgress(): JsonResponse
    {
        $sprintInfo = $this->sprintInfoRepository->getSprintInfo();
        $progress = $this->progressCalculator->calculateProgress($sprintInfo->sprint(), $sprintInfo->userStories());
        return new JsonResponse([
            'rabbit' => $progress->rabbitPosition(),
            'team' => $progress->teamPosition()
        ]);
    }
}
