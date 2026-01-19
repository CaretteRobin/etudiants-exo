<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MyExtension extends AbstractExtension
{
    public function getName(): string
    {
        return 'my-extension';
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getEnvironmentVariable', [$this, 'getEnvironmentVariable']),
            new TwigFunction('getViteAssets', [$this, 'getViteAssets']),
        ];
    }

    public function getEnvironmentVariable(string $varName): ?string
    {
        return $_ENV[$varName] ?? null;
    }

    private function manifest(): array
    {
        $jsonFilePath = __DIR__ . '/../../public/build/.vite/manifest.json';
        if (!file_exists($jsonFilePath)) {
            return [];
        }

        $jsonData = file_get_contents($jsonFilePath);
        if ($jsonData === false) {
            return [];
        }

        $decoded = json_decode($jsonData, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function getViteAssets(string $type = 'all'): string
    {
        $env = $_ENV['ENV'] ?? 'prod';

        if ($env === 'dev') {
            $scripts = [
                '<script type="module" src="http://localhost:3000/build/@vite/client"></script>',
                '<script type="module" src="http://localhost:3000/build/app.js"></script>',
            ];

            return $this->renderAssets([], $scripts, $type);
        }

        $manifest = $this->manifest();
        if (!isset($manifest['app.js'])) {
            return '';
        }

        $entry = $manifest['app.js'];
        $styles = [];
        $scripts = [];

        if (!empty($entry['css'])) {
            foreach ($entry['css'] as $cssFile) {
                $styles[] = sprintf('<link rel="stylesheet" href="/build/%s">', $cssFile);
            }
        }

        if (!empty($entry['file'])) {
            $scripts[] = sprintf('<script type="module" src="/build/%s"></script>', $entry['file']);
        }

        return $this->renderAssets($styles, $scripts, $type);
    }

    private function renderAssets(array $styles, array $scripts, string $type): string
    {
        $assets = [];
        if ($type === 'styles' || $type === 'all') {
            $assets = array_merge($assets, $styles);
        }

        if ($type === 'scripts' || $type === 'all') {
            $assets = array_merge($assets, $scripts);
        }

        return implode("\n", $assets);
    }
}
