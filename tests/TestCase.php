<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $buildDir = public_path('build');
        $assetsDir = $buildDir.DIRECTORY_SEPARATOR.'assets';

        if (! is_dir($assetsDir)) {
            mkdir($assetsDir, 0755, true);
        }

        // Only create stub assets for tests — never overwrite a real Vite build.
        $manifestPath = $buildDir.DIRECTORY_SEPARATOR.'manifest.json';
        $realCss = glob($assetsDir.DIRECTORY_SEPARATOR.'app-*.css') ?: [];
        $realJs = glob($assetsDir.DIRECTORY_SEPARATOR.'app-*.js') ?: [];

        if ($realCss && $realJs && is_file($manifestPath)) {
            $manifest = json_decode((string) file_get_contents($manifestPath), true);
            $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
            $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
            if ($cssFile && $jsFile && is_file($buildDir.DIRECTORY_SEPARATOR.$cssFile) && is_file($buildDir.DIRECTORY_SEPARATOR.$jsFile)) {
                return;
            }
        }

        file_put_contents($assetsDir.DIRECTORY_SEPARATOR.'app.css', '/* test css */');
        file_put_contents($assetsDir.DIRECTORY_SEPARATOR.'app.js', '/* test js */');
        file_put_contents($manifestPath, json_encode([
            'resources/css/app.css' => ['file' => 'assets/app.css', 'src' => 'resources/css/app.css', 'isEntry' => true],
            'resources/js/app.js' => ['file' => 'assets/app.js', 'src' => 'resources/js/app.js', 'isEntry' => true],
        ]));
    }
}
