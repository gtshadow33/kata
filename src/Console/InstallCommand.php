<?php

namespace KATA\Console;

class InstallCommand
{
    public function run(): void
    {
        $baseDirectory = getcwd();

        echo "Instalando KATA...\n\n";

        /*
        |--------------------------------------------------------------------------
        | Comprobar composer.json
        |--------------------------------------------------------------------------
        */

        $composerFile = $baseDirectory
            . DIRECTORY_SEPARATOR . 'composer.json';

        if (!file_exists($composerFile)) {
            echo "Error: no se encontró composer.json.\n";
            echo "Debes ejecutar KATA dentro de un proyecto PHP.\n";
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Crear directorios
        |--------------------------------------------------------------------------
        */

        $directories = [
            'public',
            'public/assets',
            'public/assets/css',
            'public/assets/js',
            'public/assets/images',
            'web',
        ];

        foreach ($directories as $directory) {

            $path = $baseDirectory
                . DIRECTORY_SEPARATOR
                . $directory;

            if (is_dir($path)) {
                echo "→ Ya existe: {$directory}\n";
                continue;
            }

            if (!mkdir($path, 0755, true)) {
                echo "Error: no se pudo crear {$directory}\n";
                return;
            }

            echo "✓ Creado: {$directory}\n";
        }

        /*
        |--------------------------------------------------------------------------
        | Crear archivo kata
        |--------------------------------------------------------------------------
        */

        $kataFile = $baseDirectory
            . DIRECTORY_SEPARATOR
            . 'kata';

        if (!file_exists($kataFile)) {

            $kataContent = <<<'PHP'
#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use KATA\Console\Application;

$app = new Application();

$app->run($argv);
PHP;

            if (file_put_contents($kataFile, $kataContent) === false) {
                echo "Error: no se pudo crear el archivo kata.\n";
                return;
            }

            chmod($kataFile, 0755);

            echo "✓ Creado: kata\n";

        } else {
            echo "→ Ya existe: kata\n";
        }

        /*
        |--------------------------------------------------------------------------
        | public/index.php
        |--------------------------------------------------------------------------
        */

        $indexFile = $baseDirectory
            . DIRECTORY_SEPARATOR
            . 'public'
            . DIRECTORY_SEPARATOR
            . 'index.php';

        if (!file_exists($indexFile)) {

            $indexContent = <<<'PHP'
<?php

require __DIR__ . '/../vendor/autoload.php';

$router = new \KATA\Routing\Router();

require __DIR__ . '/../web/router.php';

$router->resolve();
PHP;

            file_put_contents($indexFile, $indexContent);

            echo "✓ Creado: public/index.php\n";

        } else {
            echo "→ Ya existe: public/index.php\n";
        }

        /*
        |--------------------------------------------------------------------------
        | public/.htaccess
        |--------------------------------------------------------------------------
        */

        $htaccessFile = $baseDirectory
            . DIRECTORY_SEPARATOR
            . 'public'
            . DIRECTORY_SEPARATOR
            . '.htaccess';

        if (!file_exists($htaccessFile)) {

            $htaccessContent = <<<'HTACCESS'
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

RewriteRule ^ index.php [QSA,L]
HTACCESS;

            file_put_contents($htaccessFile, $htaccessContent);

            echo "✓ Creado: public/.htaccess\n";

        } else {
            echo "→ Ya existe: public/.htaccess\n";
        }

        /*
        |--------------------------------------------------------------------------
        | web/router.php
        |--------------------------------------------------------------------------
        */

        $routerFile = $baseDirectory
            . DIRECTORY_SEPARATOR
            . 'web'
            . DIRECTORY_SEPARATOR
            . 'router.php';

        if (!file_exists($routerFile)) {

            $routerContent = <<<'PHP'
<?php

PHP;

            file_put_contents($routerFile, $routerContent);

            echo "✓ Creado: web/router.php\n";

        } else {
            echo "→ Ya existe: web/router.php\n";
        }

        /*
        |--------------------------------------------------------------------------
        | Final
        |--------------------------------------------------------------------------
        */

        echo "\n";
        echo "========================================\n";
        echo "       KATA instalado correctamente\n";
        echo "========================================\n\n";

        echo "Ahora puedes utilizar:\n\n";

        echo "  php kata route get /users UserController@index\n";
        echo "  php kata make:controller UserController\n\n";

        echo "Instalación completada.\n";
    }
}