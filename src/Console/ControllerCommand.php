<?php

namespace KATA\Console;

class ControllerCommand
{
    public function run(array $arguments): void
    {
        /*
        |--------------------------------------------------------------------------
        | Comprobar nombre del controlador
        |--------------------------------------------------------------------------
        */

        if (!isset($arguments[2])) {
            echo "Debes indicar el nombre del controlador.\n";
            return;
        }

        $controllerName = $arguments[2];

        /*
        |--------------------------------------------------------------------------
        | Comprobar que termine en Controller
        |--------------------------------------------------------------------------
        */

        if (!str_ends_with($controllerName, 'Controller')) {
            echo "El controlador debe terminar en Controller.\n";
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Directorio del proyecto
        |--------------------------------------------------------------------------
        */

        $baseDirectory = getcwd();

        /*
        |--------------------------------------------------------------------------
        | Buscar composer.json
        |--------------------------------------------------------------------------
        */

        $composerFile = $baseDirectory
            . DIRECTORY_SEPARATOR . 'composer.json';

        if (!file_exists($composerFile)) {
            echo "No se encontró composer.json.\n";
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Leer composer.json
        |--------------------------------------------------------------------------
        */

        $composer = json_decode(
            file_get_contents($composerFile),
            true
        );

        if (!is_array($composer)) {
            echo "No se pudo leer composer.json.\n";
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Obtener namespace del proyecto
        |--------------------------------------------------------------------------
        */

        if (!isset($composer['autoload']['psr-4'])) {
            echo "No se encontró autoload PSR-4.\n";
            return;
        }

        $projectNamespace = null;

        foreach ($composer['autoload']['psr-4'] as $namespace => $directory) {

            $directory = rtrim($directory, '/\\') . '/';

            if ($directory === 'src/') {
                $projectNamespace = rtrim($namespace, '\\');
                break;
            }
        }

        if ($projectNamespace === null) {
            echo "No se encontró un namespace asociado a src/.\n";
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Crear directorio Controllers
        |--------------------------------------------------------------------------
        */

        $controllersDirectory = $baseDirectory
            . DIRECTORY_SEPARATOR
            . 'src'
            . DIRECTORY_SEPARATOR
            . 'Controllers';

        if (!is_dir($controllersDirectory)) {

            if (!mkdir($controllersDirectory, 0755, true)) {
                echo "No se pudo crear src/Controllers.\n";
                return;
            }

            echo "✓ Creado: src/Controllers\n";
        }

        /*
        |--------------------------------------------------------------------------
        | Archivo del controlador
        |--------------------------------------------------------------------------
        */

        $controllerFile = $controllersDirectory
            . DIRECTORY_SEPARATOR
            . $controllerName . '.php';

        if (file_exists($controllerFile)) {
            echo "El controlador {$controllerName} ya existe.\n";
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Namespace del controlador
        |--------------------------------------------------------------------------
        */

        $controllerNamespace =
            $projectNamespace . '\\Controllers';

        /*
        |--------------------------------------------------------------------------
        | Contenido
        |--------------------------------------------------------------------------
        */

        $controllerContent = <<<PHP
<?php

namespace {$controllerNamespace};

class {$controllerName}
{
    public function index(): void
    {
        echo "{$controllerName} funcionando";
    }
}

PHP;

        /*
        |--------------------------------------------------------------------------
        | Crear archivo
        |--------------------------------------------------------------------------
        */

        if (file_put_contents($controllerFile, $controllerContent) === false) {
            echo "No se pudo crear el controlador.\n";
            return;
        }

        echo "✓ Controlador creado correctamente.\n";
        echo "  {$controllerFile}\n";
    }
}