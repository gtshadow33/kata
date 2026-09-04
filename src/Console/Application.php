<?php

namespace KATA\Console;

class Application
{
    public function run(array $arguments): void
    {
        if (!isset($arguments[1])) {
            echo "Debes indicar un comando.\n";
            return;
        }

        switch ($arguments[1]) {
            case 'controller':
                $command = new ControllerCommand();
                $command->run($arguments);
                break;

            case 'install':
                $command = new InstallCommand();
                $command->run();
                break;

            case 'route':
                $command = new RouteCommand();
                $command->run($arguments);
                break;

            default:
                echo "Comando no encontrado.\n";
        }
    }
}