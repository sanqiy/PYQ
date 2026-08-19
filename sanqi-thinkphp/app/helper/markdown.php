<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

/**
 * Markdown渲染函数
 */

function renderMarkdownArticle(string $markdown): string
{
    $commonMarkHtml = renderMarkdownArticleWithCommonMark($markdown);
    if ($commonMarkHtml !== null) {
        return $commonMarkHtml;
    }

    $markdown = str_replace(["\r\n", "\r"], "\n", (string)$markdown);
    $lines = explode("\n", $markdown);
    $html = [];
    $paragraph = [];
    $listType = '';
    $inCode = false;
    $codeLines = [];
    $codeLanguage = '';

    $flushParagraph = function () use (&$html, &$paragraph) {
        if (empty($paragraph)) {
            return;
        }
        $html[] = '<p>' . renderMarkdownInline(implode("\n", $paragraph)) . '</p>';
        $paragraph = [];
    };
    $closeList = function () use (&$html, &$listType) {
        if ($listType !== '') {
            $html[] = '</' . $listType . '>';
            $listType = '';
        }
    };
    $renderListItem = function ($text, $taskState = null) {
        $body = renderMarkdownInline($text);
        if ($taskState !== null) {
            $checked = $taskState ? ' checked' : '';
            $body = '<input type="checkbox" disabled' . $checked . '> ' . $body;
        }
        return '<li>' . $body . '</li>';
    };

    $count = count($lines);
    for ($i = 0; $i < $count; $i++) {
        $line = $lines[$i];
        if (preg_match('/^\s*```([A-Za-z0-9_-]+)?\s*$/', $line, $match)) {
            if ($inCode) {
                if (strtolower($codeLanguage) === 'mermaid') {
                    $html[] = '<div class="sh-mermaid">' . htmlspecialchars(implode("\n", $codeLines), ENT_QUOTES, 'UTF-8') . '</div>';
                } else {
                    $class = $codeLanguage !== '' ? ' class="language-' . htmlspecialchars($codeLanguage, ENT_QUOTES, 'UTF-8') . '"' : '';
                    $html[] = '<pre><code' . $class . '>' . htmlspecialchars(implode("\n", $codeLines), ENT_QUOTES, 'UTF-8') . '</code></pre>';
                }
                $codeLines = [];
                $inCode = false;
                $codeLanguage = '';
            } else {
                $flushParagraph();
                $closeList();
                $inCode = true;
                $codeLanguage = $match[1] ?? '';
            }
            continue;
        }
        if ($inCode) {
            $codeLines[] = $line;
            continue;
        }

        $trim = trim($line);
        if ($trim === '') {
            $flushParagraph();
            $closeList();
            continue;
        }
        if (preg_match('/^([-*_])(?:\s*\1){2,}\s*$/', $trim)) {
            $flushParagraph();
            $closeList();
            $html[] = '<hr>';
            continue;
        }
        if (($i + 1) < $count && preg_match('/^(=+|-+)\s*$/', trim($lines[$i + 1]), $match) && !markdownIsTableSeparator(trim($lines[$i + 1]))) {
            $flushParagraph();
            $closeList();
            $level = substr($match[1], 0, 1) === '=' ? 1 : 2;
            $html[] = '<h' . $level . '>' . renderMarkdownInline($trim) . '</h' . $level . '>';
            $i++;
            continue;
        }
        if (($i + 1) < $count && strpos($trim, '|') !== false && markdownIsTableSeparator(trim($lines[$i + 1]))) {
            $flushParagraph();
            $closeList();
            $header = markdownParseTableRow($trim);
            $align = markdownParseTableAlign(trim($lines[$i + 1]));
            $rows = [];
            $i += 2;
            while ($i < $count) {
                $rowLine = trim($lines[$i]);
                if ($rowLine === '' || strpos($rowLine, '|') === false || markdownIsTableSeparator($rowLine)) {
                    $i--;
                    break;
                }
                $rows[] = markdownParseTableRow($rowLine);
                $i++;
            }
            $html[] = markdownRenderTable($header, $align, $rows);
            continue;
        }
        if (preg_match('/^(#{1,6})\s+(.+)$/', $trim, $match)) {
            $flushParagraph();
            $closeList();
            $level = strlen($match[1]);
            $html[] = '<h' . $level . '>' . renderMarkdownInline($match[2]) . '</h' . $level . '>';
            continue;
        }
        if (preg_match('/^>\s?(.*)$/', $trim)) {
            $flushParagraph();
            $closeList();
            $quoteLines = [];
            while ($i < $count && preg_match('/^>\s?(.*)$/', trim($lines[$i]), $quoteMatch)) {
                $quoteLines[] = $quoteMatch[1];
                $i++;
            }
            $i--;
            $html[] = '<blockquote>' . renderMarkdownArticle(implode("\n", $quoteLines)) . '</blockquote>';
            continue;
        }
        if (preg_match('/^[-*+]\s+\[([ xX])\]\s+(.+)$/', $trim, $match)) {
            $flushParagraph();
            if ($listType !== 'ul') {
                $closeList();
                $html[] = '<ul>';
                $listType = 'ul';
            }
            $html[] = $renderListItem($match[2], strtolower($match[1]) === 'x');
            continue;
        }
        if (preg_match('/^[-*+]\s+(.+)$/', $trim, $match)) {
            $flushParagraph();
            if ($listType !== 'ul') {
                $closeList();
                $html[] = '<ul>';
                $listType = 'ul';
            }
            $html[] = $renderListItem($match[1]);
            continue;
        }
        if (preg_match('/^\d+[\.)]\s+(.+)$/', $trim, $match)) {
            $flushParagraph();
            if ($listType !== 'ol') {
                $closeList();
                $html[] = '<ol>';
                $listType = 'ol';
            }
            $html[] = $renderListItem($match[1]);
            continue;
        }

        $closeList();
        $paragraph[] = $line;
    }

    if ($inCode) {
        $html[] = '<pre><code>' . htmlspecialchars(implode("\n", $codeLines), ENT_QUOTES, 'UTF-8') . '</code></pre>';
    }
    $flushParagraph();
    $closeList();

    return implode("\n", $html);
}

function renderMarkdownArticleCached(string $markdown, string $cid = ''): string
{
    $contentHash = substr(md5((string)$markdown), 0, 12);
    $cacheKey = 'md_' . ($cid ?: $contentHash);
    return \think\facade\Cache::tag('article')->remember($cacheKey, function () use ($markdown) {
        return renderMarkdownArticle($markdown);
    }, 0);
}

function renderMarkdownArticleWithCommonMark(string $markdown): ?string
{
    if (!class_exists('\\League\\CommonMark\\GithubFlavoredMarkdownConverter')) {
        return null;
    }

    try {
        $converter = new \League\CommonMark\GithubFlavoredMarkdownConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
        return (string)$converter->convert((string)$markdown);
    } catch (\Throwable $e) {
        $logDir = runtime_path('log');
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        @file_put_contents(
            $logDir . 'markdown_' . date('Ymd') . '.log',
            date('Y-m-d H:i:s') . ' ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() . "\n",
            FILE_APPEND | LOCK_EX
        );
        return null;
    }
}

function markdownIsTableSeparator(string $line): bool
{
    if (strpos((string)$line, '|') === false) {
        return false;
    }
    $cells = markdownParseTableRow($line);
    if (empty($cells)) {
        return false;
    }
    foreach ($cells as $cell) {
        if (!preg_match('/^:?-{3,}:?$/', trim($cell))) {
            return false;
        }
    }
    return true;
}

function markdownParseTableRow(string $line): array
{
    $line = trim((string)$line);
    $line = preg_replace('/^\|/', '', $line);
    $line = preg_replace('/\|$/', '', $line);
    $parts = preg_split('/(?<!\\\\)\|/', $line);
    return array_map(function ($cell) {
        return str_replace('\|', '|', trim($cell));
    }, $parts ?: []);
}

function markdownParseTableAlign(string $line): array
{
    $cells = markdownParseTableRow($line);
    return array_map(function ($cell) {
        $cell = trim($cell);
        $left = substr($cell, 0, 1) === ':';
        $right = substr($cell, -1) === ':';
        if ($left && $right) {
            return 'center';
        }
        return $right ? 'right' : ($left ? 'left' : '');
    }, $cells);
}

function markdownRenderTable(array $header, array $align, array $rows): string
{
    $html = ['<div class="sh-article-table-wrap"><table>'];
    $html[] = '<thead><tr>';
    foreach ($header as $index => $cell) {
        $style = !empty($align[$index]) ? ' style="text-align:' . $align[$index] . ';"' : '';
        $html[] = '<th' . $style . '>' . renderMarkdownInline($cell) . '</th>';
    }
    $html[] = '</tr></thead>';
    $html[] = '<tbody>';
    foreach ($rows as $row) {
        $html[] = '<tr>';
        $total = max(count($header), count($row));
        for ($index = 0; $index < $total; $index++) {
            $style = !empty($align[$index]) ? ' style="text-align:' . $align[$index] . ';"' : '';
            $html[] = '<td' . $style . '>' . renderMarkdownInline($row[$index] ?? '') . '</td>';
        }
        $html[] = '</tr>';
    }
    $html[] = '</tbody></table></div>';
    return implode('', $html);
}

function renderMarkdownInline(string $text): string
{
    $text = (string)$text;

    // 保护 LaTeX 块级公式 $$...$$
    $mathBlocks = [];
    $text = preg_replace_callback('/\$\$(.+?)\$\$/s', function ($match) use (&$mathBlocks) {
        $key = "\x1B" . count($mathBlocks) . "\x1B";
        $mathBlocks[$key] = '<div class="sh-katex-display" data-katex="' . htmlspecialchars($match[1], ENT_QUOTES, 'UTF-8') . '"></div>';
        return $key;
    }, $text);

    // 保护 LaTeX 行内公式 $...$（前后非空格、非$）
    $mathInlines = [];
    $text = preg_replace_callback('/(?<!\$)\$(?!\s)(.+?)(?<!\s)\$(?!\$)/s', function ($match) use (&$mathInlines) {
        $key = "\x1C" . count($mathInlines) . "\x1C";
        $mathInlines[$key] = '<span class="sh-katex-inline" data-katex="' . htmlspecialchars($match[1], ENT_QUOTES, 'UTF-8') . '"></span>';
        return $key;
    }, $text);

    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    $codes = [];
    $text = preg_replace_callback('/`([^`]+)`/u', function ($match) use (&$codes) {
        $key = "\x1A" . count($codes) . "\x1A";
        $codes[$key] = '<code>' . $match[1] . '</code>';
        return $key;
    }, $text);

    $text = preg_replace_callback('/!\[([^\]]*)\]\(([^)\s]+)(?:\s+&quot;[^&]*&quot;)?\)/u', function ($match) {
        $url = html_entity_decode($match[2], ENT_QUOTES, 'UTF-8');
        if (!isSafeHtmlUrl($url)) {
            return $match[1];
        }
        return '<img src="' . sanitizeUrl($url) . '" alt="' . $match[1] . '">';
    }, $text);

    $text = preg_replace_callback('/\[([^\]]+)\]\(([^)\s]+)(?:\s+&quot;[^&]*&quot;)?\)/u', function ($match) {
        $url = html_entity_decode($match[2], ENT_QUOTES, 'UTF-8');
        if (!isSafeHtmlUrl($url)) {
            return $match[1];
        }
        return '<a href="' . sanitizeUrl($url) . '" target="_blank" rel="noopener noreferrer">' . $match[1] . '</a>';
    }, $text);

    $text = preg_replace_callback('/&lt;(https?:\/\/[^&\s]+)&gt;/iu', function ($match) {
        $url = html_entity_decode($match[1], ENT_QUOTES, 'UTF-8');
        if (!isSafeHtmlUrl($url)) {
            return $match[1];
        }
        return '<a href="' . sanitizeUrl($url) . '" target="_blank" rel="noopener noreferrer">' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '</a>';
    }, $text);

    $text = preg_replace('/~~(.+?)~~/u', '<del>$1</del>', $text);
    $text = preg_replace('/\*\*\*(.+?)\*\*\*/u', '<strong><em>$1</em></strong>', $text);
    $text = preg_replace('/___(.+?)___/u', '<strong><em>$1</em></strong>', $text);
    $text = preg_replace('/\*\*(.+?)\*\*/u', '<strong>$1</strong>', $text);
    $text = preg_replace('/__(.+?)__/u', '<strong>$1</strong>', $text);
    $text = preg_replace('/(?<!\*)\*(?!\s)(.+?)(?<!\s)\*(?!\*)/u', '<em>$1</em>', $text);
    $text = preg_replace('/(?<!_)_(?!\s)(.+?)(?<!\s)_(?!_)/u', '<em>$1</em>', $text);

    if (!empty($codes)) {
        $text = strtr($text, $codes);
    }
    if (!empty($mathBlocks)) {
        $text = strtr($text, $mathBlocks);
    }
    if (!empty($mathInlines)) {
        $text = strtr($text, $mathInlines);
    }

    return nl2br($text, false);
}
