<?php
declare(strict_types=1);

namespace Perform\PrivateProjects\SprintProgress\Application;

class View
{
    public function render(string $template, array $vars = []): string
    {
        ob_clean();
        ob_start();
        eval(' ?>' . file_get_contents($this->prepareFilePath($template)) . '<?php ');
        $output = ob_get_clean();
        return $output;
    }

    private function prepareFilePath(string $template): string
    {
        $path = dirname(__DIR__) . '/Application/Templates/' . $template . '.phtml';
        if (!file_exists($path)) {
            throw new \Exception('View fatal error. Template does not exists');
        }

        return $path;
    }
}
