<?php
declare(strict_types = 1);

namespace Perform\PrivateProjects\SprintProgress\Domain\Model;

class UserStoryFactory
{
    const TYPE_USER_STORY = 'Story';
    const TYPE_TASK = 'Task';
    const TYPE_SUB_TASK = 'Sub-task';

    const STATUS_DONE = 'Done';
    const STATUS_RESOLVED = 'Resolved';
    const STATUS_CLOSED = 'Closed';
    const STATUS_TESTING = 'Testing';
    const STATUS_RESOLVED_LIST = [
        self::STATUS_DONE,
        self::STATUS_RESOLVED,
        self::STATUS_TESTING,
        self::STATUS_CLOSED,
    ];

    public function createFromArrays(array $items): UserStories
    {
        $userStories = new UserStories();
        $tasks = array_filter($items, function ($item) {
            return $item['fields']['issuetype']['name'] === self::TYPE_USER_STORY ||
                $item['fields']['issuetype']['name'] === self::TYPE_TASK;
        });

        $subTasks = array_filter($items, function ($item) {
            return $item['fields']['issuetype']['name'] === self::TYPE_SUB_TASK;
        });

        foreach ($tasks as $task) {
            $id = (int)$task['id'];
            $name = "[{$task['key']}] {$task['fields']['summary']}";
            $storyPoints = (int)(round($task['fields']['customfield_10002'] ?? 0));
            $subTasksOfTask = array_filter($subTasks, function ($item) use ($id) {
                return (int)($item['fields']['parent']['id'] ?? -1) === $id;
            });

            $doneSubTasksOfTask = array_filter($subTasksOfTask, function ($item) {
                $status = $item['fields']['status']['name'];
                return in_array($status, self::STATUS_RESOLVED_LIST);
            });

            $subTaskCount = count($subTasksOfTask) ?: 1;
            $status = $task['fields']['status']['name'];
            $subTaskDoneCount = count($subTasksOfTask)
                ? count($doneSubTasksOfTask)
                : (in_array($status, self::STATUS_RESOLVED_LIST) ? 1 : 0);

            $userStory = new UserStory(
                $id,
                $name,
                $storyPoints,
                $subTaskCount,
                $subTaskDoneCount
            );
            $userStories->add($userStory);
        }

        return $userStories;
    }
}
