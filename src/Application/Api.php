<?php
declare(strict_types = 1);

namespace Perform\PrivateProjects\SprintProgress\Application;

use GuzzleHttp\Client;
use Perform\Component\Serializer\Factory;
use Perform\Component\Serializer\Serializer;
use Perform\PrivateProjects\SprintProgress\Domain\Model\ProgressCalculator;
use Perform\PrivateProjects\SprintProgress\Domain\Model\ProgressFactory;
use Perform\PrivateProjects\SprintProgress\Domain\Model\SprintFactory;
use Perform\PrivateProjects\SprintProgress\Domain\Model\UserStoryFactory;
use Perform\PrivateProjects\SprintProgress\Domain\Service\ISprintInfoRepository;
use Perform\PrivateProjects\SprintProgress\Infrastructure\JiraApiClient;
use Perform\PrivateProjects\SprintProgress\Infrastructure\SprintInfoRepository;
use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;
use Symfony\Component\HttpFoundation\Request;

class Api extends Application
{
    public function boot(): void
    {
        parent::boot();
        $this->setProviders();
        $this->setDependencyInjection();
        $this->setRouting();
    }

    public function run(Request $request = null)
    {
        parent::run($request);
    }

    protected function setRouting(): void
    {
        $this->get('/api/progress/current', Controller::class . ':getProgress');
        $this->get('/', Controller::class . ':getProgressHtmlWhite');
        $this->get('/dark', Controller::class . ':getProgressHtmlDark');
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

        $this[Serializer::class] = function () {
            return Factory::create(null, \DateTime::ISO8601, Serializer::ERROR_MODE_THROW);
        };

        $this[View::class] = function () {
            return new View();
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
                $this[ISprintInfoRepository::class],
                $this[View::class],
                $this[Serializer::class]
            );
        };
    }
}
