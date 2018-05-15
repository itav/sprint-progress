<?php
declare(strict_types=1);

namespace Perform\PrivateProjects\SprintProgress\Application;

use Perform\Component\Serializer\Serializer;
use Perform\PrivateProjects\SprintProgress\Domain\Model\ProgressCalculator;
use Perform\PrivateProjects\SprintProgress\Domain\Service\ISprintInfoRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
    private $progressCalculator;
    private $sprintInfoRepository;
    private $view;
    private $serializer;

    public function __construct(
        ProgressCalculator $progressCalculator,
        ISprintInfoRepository $sprintInfoRepository,
        View $view,
        Serializer $serializer
    ) {
        $this->sprintInfoRepository = $sprintInfoRepository;
        $this->progressCalculator = $progressCalculator;
        $this->view = $view;
        $this->serializer = $serializer;
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

    public function getProgressHtmlWhite(): Response
    {
        return $this->getProgressHtml('main');
    }

    public function getProgressHtmlDark(): Response
    {
        return $this->getProgressHtml('main-dark');
    }

    private function getProgressHtml(string $template): Response
    {
        try {
            $sprintInfo = $this->sprintInfoRepository->getSprintInfo();
            $progress = $this->progressCalculator->calculateProgress($sprintInfo->sprint(), $sprintInfo->userStories());
            $sprintInfoData = $this->serializer->normalize($sprintInfo, false, true);
            $progressData = $this->serializer->normalize($progress, false, true);
            $html = $this->view->render($template, ['sprint' => $sprintInfoData, 'progress' => $progressData]);
            return new Response($html);
        } catch (\Throwable $exception) {
            $html = $this->view->render('error', ['msg' => $exception->getMessage()]);
            return new Response($html);
        }
    }
}
