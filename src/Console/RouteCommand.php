<?php

namespace KATA\Console;

class RouteCommand
{
    public function run(array $arguments): void
    {
        /*
        |--------------------------------------------------------------------------
        | Comprobar método
        |--------------------------------------------------------------------------
        */

        if (!isset($arguments[2])) {
            echo "Debes indicar el método: get o post.\n";
            return;
        }

        $method = strtolower($arguments[2]);

        if (!in_array($method, ['get', 'post'], true)) {
            echo "Método no válido. Usa get o post.\n";
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Comprobar ruta
        |--------------------------------------------------------------------------
        */

        if (!isset($arguments[3])) {
            echo "Debes indicar la ruta.\n";
            return;
        }

        $path = $arguments[3];

        /*
        |--------------------------------------------------------------------------
        | Comprobar controlador
        |--------------------------------------------------------------------------
        */

        if (!isset($arguments[4])) {
            echo "Debes indicar el controlador.\n";
            return;
        }

        $controller = $arguments[4];

        if (!str_contains($controller, '@')) {
            echo "El controlador debe tener el formato Controller@method.\n";
            return;
        }

        [$controllerName, $controllerMethod] = explode('@', $controller, 2);

        if ($controllerName === '' || $controllerMethod === '') {
            echo "El controlador no puede estar vacío.\n";
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Buscar composer.json
        |--------------------------------------------------------------------------
        */

        $baseDirectory = getcwd();

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
            echo "No se encontró autoload PSR-4 en composer.json.\n";
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
            echo "No se encontró un namespace PSR-4 asociado a src/.\n";
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Namespace del controlador
        |--------------------------------------------------------------------------
        */

        $controllerNamespace =
            $projectNamespace .
            '\\Controllers\\' .
            $controllerName;

        /*
        |--------------------------------------------------------------------------
        | Directorio web
        |--------------------------------------------------------------------------
        */

        $webDirectory = $baseDirectory
            . DIRECTORY_SEPARATOR . 'web';

        if (!is_dir($webDirectory)) {
            mkdir($webDirectory, 0755, true);

            echo "✓ Directorio web creado.\n";
        }

        /*
        |--------------------------------------------------------------------------
        | Archivo de rutas
        |--------------------------------------------------------------------------
        */

        $routesFile = $webDirectory
            . DIRECTORY_SEPARATOR . 'router.php';

        if (!file_exists($routesFile)) {

            $routerContent = <<<'PHP'
<?php

PHP;

            file_put_contents($routesFile, $routerContent);

            echo "✓ web/router.php creado.\n";
        }

        /*
        |--------------------------------------------------------------------------
        | Leer rutas actuales
        |--------------------------------------------------------------------------
        */

        $routesContent = file_get_contents($routesFile);

        /*
        |--------------------------------------------------------------------------
        | Añadir use del controlador
        |--------------------------------------------------------------------------
        */

        $useStatement = "use {$controllerNamespace};";

        if (!str_contains($routesContent, $useStatement)) {

            $lines = explode(PHP_EOL, $routesContent);

            /*
             * Insertamos el use después de <?php
             */
            array_splice($lines, 1, 0, $useStatement);

            $routesContent = implode(PHP_EOL, $lines);

            file_put_contents(
                $routesFile,
                $routesContent
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Crear ruta
        |--------------------------------------------------------------------------
        */

        $route =
            '$router->' .
            $method .
            "('$path', [$controllerName::class, '$controllerMethod']);";

        /*
        |--------------------------------------------------------------------------
        | Comprobar si ya existe
        |--------------------------------------------------------------------------
        */

        if (str_contains($routesContent, $route)) {
            echo "La ruta ya existe.\n";
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Añadir ruta
        |--------------------------------------------------------------------------
        */

        file_put_contents(
            $routesFile,
            $route . PHP_EOL,
            FILE_APPEND
        );

        echo "✓ Ruta creada correctamente.\n";
        echo "  {$route}\n";
    }
}