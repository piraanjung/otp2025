#!/usr/bin/env php
<?php

/**
 * Semi-automatic audit: Blade views not reachable from PHP view() (and Blade transitive refs),
 * and public/ files possibly never referenced via asset()/mix() and common URL patterns.
 *
 * Usage:
 *   php scripts/audit-blade-and-public.php              # both sections
 *   php scripts/audit-blade-and-public.php --views-only
 *   php scripts/audit-blade-and-public.php --public-only
 *   php scripts/audit-blade-and-public.php --public-only --public-summary
 *
 * Limitations (read output as hints, not proof):
 *   - Dynamic names: view($var), @include($tpl), component($x), tags <x-dynamic-component>
 *   - Vendor / package views, Livewire classes, Mail markdown, notifications
 *   - Strings built by concatenation, translated paths, config-driven templates
 *   - public/: only static string matches in scanned files; Vite handled simply; storage/ not scanned
 */

declare(strict_types=1);

$opts = getopt('', ['views-only', 'public-only', 'public-summary', 'help'], $optind);
if (isset($opts['help'])) {
    echo "Usage: php scripts/audit-blade-and-public.php [--views-only] [--public-only] [--public-summary]\n";
    exit(0);
}

$doViews = ! isset($opts['public-only']);
$doPublic = ! isset($opts['views-only']);

$root = dirname(__DIR__);
$viewsDir = $root . '/resources/views';
$publicDir = $root . '/public';

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function bladePathToViewName(string $fullPath, string $viewsDir): ?string
{
    $rel = str_replace('\\', '/', substr($fullPath, strlen(rtrim($viewsDir, '/')) + 1));
    if ($rel === false || $rel === '') {
        return null;
    }
    $rel = preg_replace('#\.blade\.php$#D', '', $rel) ?? $rel;
    $rel = preg_replace('#\.php$#D', '', $rel) ?? $rel;
    return str_replace('/', '.', $rel);
}

/**
 * @return array<string, string> viewName => absolute path
 */
function indexBladeFiles(string $viewsDir): array
{
    $map = [];
    if (! is_dir($viewsDir)) {
        return $map;
    }
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($viewsDir, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($it as $f) {
        if (! $f->isFile()) {
            continue;
        }
        $n = $f->getFilename();
        if (! str_ends_with($n, '.blade.php') && ! str_ends_with($n, '.php')) {
            continue;
        }
        $name = bladePathToViewName($f->getPathname(), $viewsDir);
        if ($name !== null) {
            $map[$name] = $f->getPathname();
        }
    }
    return $map;
}

/**
 * Extract view name literals from PHP/Blade source.
 *
 * @return list<string>
 */
function extractViewLiteralsFromText(string $code): array
{
    $found = [];

    $patterns = [
        // view/mail/route response
        '/\b(?:view|response)\s*\(\s*[\'"]([a-zA-Z0-9@_\.-]+)[\'"]/m',
        '/\bRoute::view\s*\(\s*[^,]+,\s*[\'"]([a-zA-Z0-9@_\.-]+)[\'"]/m',
        '/\bView::(?:make|first|file)\s*\(\s*[\'"]([a-zA-Z0-9@_\.-]+)[\'"]/m',
        '/\b(?:markdown|html|text)\s*\(\s*[\'"]([a-zA-Z0-9@_\.-]+)[\'"]/m', // Mail::markdown('...')
    ];

    foreach ($patterns as $re) {
        if (preg_match_all($re, $code, $m)) {
            foreach ($m[1] as $v) {
                $found[] = $v;
            }
        }
    }

    // @extends('layouts.app') — allow optional ()
    $bladeRe = [
        '/@extends\s*\(\s*[\'"]([a-zA-Z0-9@_\.-]+)[\'"]\s*\)/',
        '/@extends\s+[\'"]([a-zA-Z0-9@_\.-]+)[\'"]/',
        '/@include(?:If|Unless|When)?\s*\(\s*[\'"]([a-zA-Z0-9@_\.-]+)[\'"]/',
        '/@each\s*\(\s*[\'"]([a-zA-Z0-9@_\.-]+)[\'"]/',
        '/@component\s*\(\s*[\'"]([a-zA-Z0-9@_\.-]+)[\'"]/',
        '/@includeFirst\s*\(\s*\[\s*([^\]]+)\]/',
    ];

    foreach ($bladeRe as $re) {
        if (preg_match_all($re, $code, $m)) {
            foreach ($m[1] as $chunk) {
                if (str_contains($chunk, '[')) {
                    continue;
                }
                if (preg_match_all('/[\'"]([a-zA-Z0-9@_\.-]+)[\'"]/', $chunk, $mm)) {
                    foreach ($mm[1] as $v) {
                        $found[] = $v;
                    }
                } else {
                    $found[] = $chunk;
                }
            }
        }
    }

    // <x-name.nested />  → components.name.nested
    if (preg_match_all('/<x-([a-zA-Z0-9._-]+)(\s|\/|>)/', $code, $xm)) {
        foreach ($xm[1] as $tag) {
            if (str_starts_with($tag, 'slot') || $tag === 'dynamic-component') {
                continue;
            }
            // Anonymous component: resources/views/components/foo/bar.blade.php → components.foo.bar
            $found[] = 'components.' . $tag;
        }
    }

    return array_values(array_unique($found));
}

/**
 * Normalize a public URL path as stored in asset('foo/bar.css').
 */
function normalizePublicKey(string $path): string
{
    $path = trim($path);
    $path = preg_replace('#^/+/#', '/', '/' . ltrim($path, '/')) ?? $path;
    return ltrim($path, '/');
}

/**
 * @return list<string> keys like adminlte/dist/foo.css
 */
function extractPublicLiteralsFromText(string $code): array
{
    $out = [];
    $patterns = [
        '/\basset\s*\(\s*[\'"]([^\'"]+)[\'"]/',
        '/\bsecure_asset\s*\(\s*[\'"]([^\'"]+)[\'"]/',
        '/\bmix\s*\(\s*[\'"]([^\'"]+)[\'"]/',
        '/Vite::asset\s*\(\s*[\'"]([^\'"]+)[\'"]/',
    ];
    foreach ($patterns as $re) {
        if (preg_match_all($re, $code, $m)) {
            foreach ($m[1] as $p) {
                $out[] = normalizePublicKey($p);
            }
        }
    }
    // @vite(['resources/css/app.css', ...]) — only report resources/* hints, skip for public map
    return array_values(array_unique(array_filter($out)));
}

/** @return list<string> absolute file paths */
function collectScanFiles(string $root): array
{
    $dirs = [
        $root . '/app',
        $root . '/routes',
        $root . '/bootstrap',
        $root . '/config',
        $root . '/database',
        $root . '/resources/views',
        $root . '/resources/js',
    ];
    $extra = [
        $root . '/webpack.mix.js',
        $root . '/vite.config.js',
        $root . '/vite.config.mjs',
        $root . '/vite.config.ts',
    ];
    $files = [];
    foreach ($dirs as $dir) {
        if (! is_dir($dir)) {
            continue;
        }
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($it as $f) {
            if (! $f->isFile()) {
                continue;
            }
            $fn = $f->getFilename();
            if (preg_match('/\.(?:php|blade\.php|js|ts|vue|jsx|tsx|json)$/i', $fn)) {
                $files[] = $f->getPathname();
            }
        }
    }
    foreach ($extra as $f) {
        if (is_file($f)) {
            $files[] = $f;
        }
    }
    return array_values(array_unique($files));
}

// ---------------------------------------------------------------------------
// Blade reachability
// ---------------------------------------------------------------------------
if ($doViews) {
    echo "=== Blade views (resources/views) ===\n\n";

    $bladeMap = indexBladeFiles($viewsDir);
    $reachable = [];
    $queue = [];

    $scanFiles = collectScanFiles($root);
    $phpBladeCodeFiles = array_values(array_filter($scanFiles, function ($p) {
        return (bool) preg_match('/\.(?:php|blade\.php)$/i', $p);
    }));

    foreach ($phpBladeCodeFiles as $path) {
        $code = @file_get_contents($path);
        if ($code === false) {
            continue;
        }
        foreach (extractViewLiteralsFromText($code) as $name) {
            if (! isset($reachable[$name])) {
                $reachable[$name] = true;
                $queue[] = $name;
            }
        }
    }

    // Fixed-point: follow @include / @extends inside reachable blades
    $guard = 0;
    while ($queue !== [] && $guard < 5000) {
        $guard++;
        $name = array_shift($queue);
        if (! isset($bladeMap[$name])) {
            continue;
        }
        $code = @file_get_contents($bladeMap[$name]);
        if ($code === false) {
            continue;
        }
        foreach (extractViewLiteralsFromText($code) as $child) {
            if (! isset($reachable[$child])) {
                $reachable[$child] = true;
                $queue[] = $child;
            }
        }
    }

    $unused = [];
    foreach (array_keys($bladeMap) as $name) {
        if (! isset($reachable[$name])) {
            $unused[] = $name;
        }
    }
    sort($unused);

    echo 'Indexed blade files: ' . count($bladeMap) . "\n";
    echo 'Reachable view names: ' . count($reachable) . "\n";
    echo 'Possibly unused (not in closure from PHP/Blade literals): ' . count($unused) . "\n\n";

    if ($unused === []) {
        echo "(none — or views dir missing)\n\n";
    } else {
        foreach ($unused as $u) {
            $rel = str_replace($root . '/', '', $bladeMap[$u]);
            echo $u . '  →  ' . $rel . "\n";
        }
        echo "\n";
    }

    echo "Note: names only referenced dynamically (variable/builder) appear as false positives here.\n\n";
}

// ---------------------------------------------------------------------------
// Public / asset()
// ---------------------------------------------------------------------------
if ($doPublic) {
    echo "=== Public files (public/) vs literal asset()/mix() in scanned code ===\n\n";

    if (! is_dir($publicDir)) {
        echo "public/ missing.\n";
        exit(0);
    }

    $referenced = [];
    foreach (collectScanFiles($root) as $path) {
        $code = @file_get_contents($path);
        if ($code === false) {
            continue;
        }
        foreach (extractPublicLiteralsFromText($code) as $key) {
            if ($key === '' || str_starts_with($key, 'http') || str_starts_with($key, '//')) {
                continue;
            }
            $referenced[$key] = true;
            // Also mark directory prefix coverage isn't trivial; store exact keys only
        }
    }

    // Index public files (relative paths)
    $publicFiles = [];
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($publicDir, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($it as $f) {
        if (! $f->isFile()) {
            continue;
        }
        $rel = str_replace('\\', '/', substr($f->getPathname(), strlen(rtrim($publicDir, '/\\')) + 1));
        $publicFiles[] = $rel;
    }
    sort($publicFiles);

    $unmatched = [];
    foreach ($publicFiles as $rel) {
        if (str_contains($rel, '/vendor/') || str_contains($rel, 'node_modules')) {
            continue;
        }
        // Exact match
        if (isset($referenced[$rel])) {
            continue;
        }
        // Query-string-stripped reference in code won't match; try basename only for loose hint (too noisy) — skip
        $unmatched[] = $rel;
    }

    // Remove files that are a prefix of any referenced file? Actually opposite: if code refs adminlte/dist/css/adminlte.min.css, exact exists.
    // If code uses only adminlte/dist/css/adminlte.min.css, other adminlte files show unmatched — correct heuristic "maybe unused"

    echo 'Files under public/: ' . count($publicFiles) . "\n";
    echo 'Unique literal paths from asset/mix/Vite::asset: ' . count($referenced) . "\n";
    echo 'Public files never matching a referenced path exactly: ' . count($unmatched) . "\n\n";

    if ($unmatched === []) {
        echo "(none)\n\n";
    } elseif (isset($opts['public-summary'])) {
        $byTop = [];
        foreach ($unmatched as $u) {
            $top = explode('/', $u, 2)[0];
            $byTop[$top] = ($byTop[$top] ?? 0) + 1;
        }
        arsort($byTop);
        foreach ($byTop as $top => $cnt) {
            echo sprintf("%5d  %s\n", $cnt, $top);
        }
        echo "\n(Run without --public-summary for full file list.)\n\n";
    } else {
        foreach ($unmatched as $u) {
            echo $u . "\n";
        }
        echo "\n";
    }

    echo "Note: many frameworks ship many files under one folder while only a few paths are referenced.\n";
    echo "      Also: dynamic asset(\$var), symlink storage, and Mix manifest hashes are not modeled.\n\n";
}

echo "Done.\n";
