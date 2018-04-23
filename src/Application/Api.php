<?php
declare(strict_types = 1);

namespace Perform\PrivateProjects\SprintProgress\Application;

use GuzzleHttp\Client;
use Perform\PrivateProjects\SprintProgress\Domain\Model\ProgressCalculator;
use Perform\PrivateProjects\SprintProgress\Domain\Model\ProgressFactory;
use Perform\PrivateProjects\SprintProgress\Domain\Model\SprintFactory;
use Perform\PrivateProjects\SprintProgress\Domain\Model\UserStoryFactory;
use Perform\PrivateProjects\SprintProgress\Domain\Service\ISprintInfoRepository;
use Perform\PrivateProjects\SprintProgress\Infrastructure\JiraApiClient;
use Perform\PrivateProjects\SprintProgress\Infrastructure\SprintInfoRepository;
use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;

class Api extends Application
{
    public function boot(): void
    {
        parent::boot();
        $this->setProviders();
        $this->setDependencyInjection();
        $this->setRouting();
    }

    protected function setRouting(): void
    {
        $this->get('/progress/current', Controller::class . ':getProgress');
    }

    protected function setProviders(): void
    {
        $this->register(new ServiceControllerServiceProvider());
    }

    protected function setDependencyInjection(): void
    {
        $this[JiraApiClient::class] = function () {
            return new JiraApiClient(new Client());
        };

        $this[ISprintInfoRepository::class] = function () {
            return new SprintInfoRepository(
                $this[JiraApiClient::class],
                new SprintFactory(),
                new UserStoryFactory()
            );
        };

        $this[ProgressCalculator::class] = function () {
            return new ProgressCalculator(new ProgressFactory());
        };

        $this[Controller::class] = function () {
            return new Controller(
                $this[ProgressCalculator::class],
                $this[ISprintInfoRepository::class]
            );
        };
    }
}
