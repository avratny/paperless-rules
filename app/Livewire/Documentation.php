<?php

namespace App\Livewire;

use Illuminate\Support\Facades\File;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;
use Livewire\Attributes\Url;
use Livewire\Component;

class Documentation extends Component
{
    public string $currentLanguage;
    public array $navigation = [];

    #[Url(as: 'page', keep: true)]
    public string $currentFile = '';

    public string $renderedContent = '';
    public string $pageTitle = '';

    public string $searchQuery = '';
    public array $searchResults = [];
    public bool $showSearchResults = false;

    public function getActiveSectionProperty(): string
    {
        foreach ($this->navigation as $sectionKey => $section) {
            if ($sectionKey === 'Start') {
                continue;
            }

            // Check if current file is in this section
            if (!empty($section['children'])) {
                foreach ($section['children'] as $child) {
                    if ($child['file'] === $this->currentFile) {
                        return $sectionKey;
                    }
                }
            } elseif (!empty($section['files'])) {
                if (in_array($this->currentFile, $section['files'])) {
                    return $sectionKey;
                }
            }
        }

        // Default to first section after Start
        $keys = array_keys($this->navigation);
        foreach ($keys as $key) {
            if ($key !== 'Start') {
                return $key;
            }
        }

        return '';
    }

    public function mount(): void
    {
        $this->currentLanguage = app()->getLocale();
        $this->loadNavigation();

        // Load from URL or default to first document
        if (empty($this->currentFile) && !empty($this->navigation)) {
            $firstSection = array_values($this->navigation)[0];
            if (isset($firstSection['file'])) {
                $this->currentFile = $firstSection['file'];
            } elseif (isset($firstSection['files']) && !empty($firstSection['files'])) {
                $this->currentFile = $firstSection['files'][0];
            }
        }

        $this->loadDocument();
    }

    public function updatedCurrentLanguage(): void
    {
        $this->loadNavigation();
        $this->loadDocument();
    }

    public function updatedSearchQuery(): void
    {
        if (strlen($this->searchQuery) < 3) {
            $this->searchResults = [];
            $this->showSearchResults = false;
            return;
        }

        $this->searchResults = $this->searchInMarkdownFiles($this->searchQuery);
        $this->showSearchResults = true;
    }

    public function clearSearch(): void
    {
        $this->searchQuery = '';
        $this->searchResults = [];
        $this->showSearchResults = false;
    }

    public function loadNavigation(): void
    {
        $docConfig = config('docs.doc', []);

        if (!isset($docConfig[$this->currentLanguage])) {
            $this->navigation = [];
            return;
        }

        $config = $docConfig[$this->currentLanguage];
        $this->navigation = [];

        foreach ($config as $key => $section) {
            $navItem = [
                'title' => $section['title'] ?? $key,
                'files' => [],
                'children' => []
            ];

            if (isset($section['file'])) {
                $navItem['file'] = $section['file'];
                $navItem['files'] = [$section['file']];
            } elseif (isset($section['children'])) {
                // New structure with explicit children
                foreach ($section['children'] as $child) {
                    $navItem['children'][] = [
                        'title' => $child['title'],
                        'file' => $child['file']
                    ];
                    $navItem['files'][] = $child['file'];
                }
            } elseif (isset($section['files'])) {
                // Legacy structure with file patterns
                foreach ($section['files'] as $pattern) {
                    $files = $this->resolveFilePattern($pattern);
                    $navItem['files'] = array_merge($navItem['files'], $files);
                }
            }

            $this->navigation[$key] = $navItem;
        }
    }

    protected function resolveFilePattern(string $pattern): array
    {
        $basePath = resource_path("docs/{$this->currentLanguage}/");

        if (str_contains($pattern, '*')) {
            $fullPattern = $basePath . $pattern;
            $files = glob($fullPattern);

            return array_map(function ($file) use ($basePath) {
                return str_replace($basePath, '', $file);
            }, $files);
        }

        return [$pattern];
    }

    public function selectDocument(string $file): void
    {
        $this->currentFile = $file;
        $this->loadDocument();
        $this->dispatch('content-updated');
    }

    protected function loadDocument(): void
    {
        if (empty($this->currentFile)) {
            $this->renderedContent = '';
            $this->pageTitle = '';
            return;
        }

        $filePath = resource_path("docs/{$this->currentLanguage}/{$this->currentFile}");

        if (!File::exists($filePath)) {
            $this->renderedContent = '<p class="text-red-400">Document not found.</p>';
            $this->pageTitle = 'Not Found';
            return;
        }

        $markdown = File::get($filePath);

        // Configure environment with extensions
        $config = [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ];

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new FrontMatterExtension());
        $environment->addExtension(new TableExtension());

        $converter = new MarkdownConverter($environment);
        $result = $converter->convert($markdown);

        // Process internal links
        $html = $result instanceof RenderedContentWithFrontMatter
            ? $result->getContent()
            : $result->getContent();
        $html = $this->processInternalLinks($html);

        if ($result instanceof RenderedContentWithFrontMatter) {
            $frontMatter = $result->getFrontMatter();
            $this->pageTitle = $frontMatter['title'] ?? basename($this->currentFile, '.md');
            $this->renderedContent = $html;
        } else {
            $this->pageTitle = basename($this->currentFile, '.md');
            $this->renderedContent = $html;
        }
    }

    protected function processInternalLinks(string $html): string
    {
        // Convert internal .md links to wire:click calls
        return preg_replace_callback(
            '/<a href="([^"]+\.md)">(.*?)<\/a>/',
            function ($matches) {
                $href = $matches[1];
                $text = $matches[2];

                // Resolve relative path
                $currentDir = dirname($this->currentFile);
                $targetFile = $this->resolveRelativePath($currentDir, $href);

                // Return wire:click link
                return sprintf(
                    '<a href="#" wire:click.prevent="selectDocument(\'%s\')" class="cursor-pointer">%s</a>',
                    htmlspecialchars($targetFile, ENT_QUOTES),
                    $text
                );
            },
            $html
        );
    }

    protected function resolveRelativePath(string $currentDir, string $relativePath): string
    {
        // Handle ../
        if (str_starts_with($relativePath, '../')) {
            $parts = explode('/', $currentDir);
            $relativeParts = explode('/', $relativePath);

            foreach ($relativeParts as $part) {
                if ($part === '..') {
                    array_pop($parts);
                } elseif ($part !== '.' && $part !== '') {
                    $parts[] = $part;
                }
            }

            return implode('/', $parts);
        }

        // Handle ./  or direct file
        if (str_starts_with($relativePath, './')) {
            $relativePath = substr($relativePath, 2);
        }

        if ($currentDir === '.' || $currentDir === '') {
            return $relativePath;
        }

        return $currentDir . '/' . $relativePath;
    }

    protected function searchInMarkdownFiles(string $query): array
    {
        $results = [];
        $basePath = resource_path("docs/{$this->currentLanguage}/");

        // Durchsuche alle Dateien aus der Navigation
        foreach ($this->navigation as $sectionKey => $section) {
            foreach ($section['files'] as $file) {
                $filePath = $basePath . $file;

                if (!File::exists($filePath)) {
                    continue;
                }

                $content = File::get($filePath);
                $lines = explode("\n", $content);

                // Suche in jeder Zeile
                foreach ($lines as $lineNumber => $line) {
                    if (stripos($line, $query) !== false) {
                        $results[] = [
                            'file' => $file,
                            'section' => $section['title'],
                            'title' => $this->getDocumentTitle($content),
                            'line' => $lineNumber + 1,
                            'snippet' => $this->createSnippet($lines, $lineNumber, $query),
                            'relevance' => $this->calculateRelevance($line, $query)
                        ];

                        // Nur ersten Treffer pro Datei nehmen
                        break;
                    }
                }
            }
        }

        // Sortiere nach Relevanz
        usort($results, fn($a, $b) => $b['relevance'] <=> $a['relevance']);

        return $results;
    }

    protected function createSnippet(array $lines, int $lineNumber, string $query): string
    {
        // Zeige 2 Zeilen vor und nach dem Treffer
        $start = max(0, $lineNumber - 1);
        $end = min(count($lines) - 1, $lineNumber + 1);

        $snippet = '';
        for ($i = $start; $i <= $end; $i++) {
            $line = strip_tags($lines[$i]);

            // Entferne Markdown-Syntax für bessere Lesbarkeit
            $line = preg_replace('/^#+\s+/', '', $line); // Headers
            $line = preg_replace('/\*\*(.+?)\*\*/', '$1', $line); // Bold
            $line = preg_replace('/\*(.+?)\*/', '$1', $line); // Italic
            $line = preg_replace('/`(.+?)`/', '$1', $line); // Code

            if (strlen($line) > 150) {
                $line = substr($line, 0, 150) . '...';
            }

            // Highlight den Suchbegriff
            $highlighted = preg_replace(
                '/(' . preg_quote($query, '/') . ')/i',
                '<mark class="bg-yellow-400 text-gray-900 px-1 rounded">$1</mark>',
                htmlspecialchars($line)
            );

            if ($i === $lineNumber) {
                $snippet .= '<strong class="text-white">' . $highlighted . '</strong>';
            } else {
                $snippet .= '<span class="text-gray-400">' . $highlighted . '</span>';
            }

            if ($i < $end) {
                $snippet .= ' ';
            }
        }

        return $snippet;
    }

    protected function calculateRelevance(string $line, string $query): int
    {
        $relevance = 0;

        // Höhere Relevanz für Überschriften
        if (preg_match('/^#+\s/', $line)) {
            $relevance += 10;
        }

        // Höhere Relevanz für exakte Treffer
        if (stripos($line, $query) !== false) {
            $relevance += 5;
        }

        // Höhere Relevanz wenn am Anfang der Zeile
        if (stripos(trim($line), $query) === 0) {
            $relevance += 3;
        }

        return $relevance;
    }

    protected function getDocumentTitle(string $content): string
    {
        // Extrahiere ersten H1-Header
        if (preg_match('/^#\s+(.+)$/m', $content, $matches)) {
            return trim($matches[1]);
        }

        return 'Untitled';
    }

    public function render()
    {
        return view('livewire.documentation');
    }
}

