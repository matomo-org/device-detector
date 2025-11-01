#!/usr/bin/env php
<?php

if ('cli' !== php_sapi_name()) {
    echo 'web not supported';
    exit(0);
}

class RegexOptimizer
{
    public function optimize(string $pattern): string
    {
        // Сначала обрабатываем группы
        $result = $this->processGroups($pattern);

        // Затем — альтернативы верхнего уровня
        $topLevelParts = $this->splitTopLevelPipe($result);
        if (count($topLevelParts) > 1) {
            $optimized = $this->optimizeAlternation($result);
            if ($optimized !== null && $optimized !== $result) {
                return $optimized;
            }
        }

        return $result;
    }

    private function processGroups(string $pattern): string
    {
        return preg_replace_callback(
            '/$$ (?: \?: | \?= | \?! | \<\w+\> )? ( [^()]++ ) $$/x',
            function ($matches) {
                $prefix = $matches[1] ?? '';
                $inner = $matches[2];
                $optimized = $this->optimizeAlternation($inner);
                if ($optimized === null) {
                    return '(' . $prefix . $inner . ')';
                }
                return '(' . $prefix . $optimized . ')';
            },
            $pattern
        );
    }

    private function splitTopLevelPipe(string $str): array
    {
        $parts = [];
        $start = 0;
        $depth = 0;
        $len = strlen($str);

        for ($i = 0; $i < $len; $i++) {
            if ($i + 1 < $len && $str[$i] === '\\') {
                $i++;
                continue;
            }
            if ($str[$i] === '(') $depth++;
            elseif ($str[$i] === ')') $depth--;
            elseif ($str[$i] === '|' && $depth === 0) {
                $parts[] = substr($str, $start, $i - $start);
                $start = $i + 1;
            }
        }
        $parts[] = substr($str, $start);
        return array_filter($parts, 'strlen');
    }

    private function optimizeAlternation(string $content): ?string
    {
        $parts = $this->splitTopLevelPipe($content);
        if (count($parts) < 2) {
            return null;
        }

        // Удаляем дубликаты
        $parts = array_unique($parts);
        if (count($parts) < 2) {
            return implode('|', $parts);
        }

        // 1. Односимвольные → [abc]
        if ($this->allSingleChars($parts)) {
            $chars = array_map([$this, 'unescape'], $parts);
            sort($chars);
            $collapsed = $this->collapseToRanges($chars);
            return '[' . $collapsed . ']';
        }

        // 2. Общий префикс/суффикс
        $prefix = $this->commonPrefix($parts);
        $suffix = $this->commonSuffix($parts);

        if (strlen($prefix) > 0 || strlen($suffix) > 0) {
            if ($this->canTrimAll($parts, $prefix, $suffix)) {
                $pLen = strlen($prefix);
                $sLen = strlen($suffix);
                $midParts = array_map(
                    fn($p) => $sLen ? substr($p, $pLen, -$sLen) : substr($p, $pLen),
                    $parts
                );

                $inner = implode('|', $midParts);
                $optimizedInner = $this->optimizeAlternation($inner);
                $result = $prefix . ($optimizedInner ?? $inner) . $suffix;

                return $result !== $content ? $result : null;
            }
        }

        return null;
    }

    private function allSingleChars(array $parts): bool
    {
        foreach ($parts as $p) {
            if (strlen($p) === 1) continue;
            if (strlen($p) === 2 && $p[0] === '\\') continue;
            return false;
        }
        return true;
    }

    private function unescape(string $str): string
    {
        return preg_replace('/^\\\\(.)/', '$1', $str);
    }

    private function commonPrefix(array $strings): string
    {
        if (empty($strings)) return '';
        $first = $strings[0];
        $len = strlen($first);
        $prefix = '';
        for ($i = 0; $i < $len; $i++) {
            $char = $first[$i];
            foreach ($strings as $str) {
                if (!isset($str[$i]) || $str[$i] !== $char) {
                    return $prefix;
                }
            }
            $prefix .= $char;
        }
        return $prefix;
    }

    private function commonSuffix(array $strings): string
    {
        if (empty($strings)) return '';
        $minLen = min(array_map('strlen', $strings));
        $suffix = '';
        for ($i = 1; $i <= $minLen; $i++) {
            $char = $strings[0][strlen($strings[0]) - $i];
            foreach ($strings as $str) {
                if ($str[strlen($str) - $i] !== $char) {
                    return $suffix;
                }
            }
            $suffix = $char . $suffix;
        }
        return $suffix;
    }

    private function canTrimAll(array $parts, string $prefix, string $suffix): bool
    {
        $pLen = strlen($prefix);
        $sLen = strlen($suffix);
        foreach ($parts as $part) {
            $len = strlen($part);
            if ($len < $pLen + $sLen) return false;
            if (substr($part, 0, $pLen) !== $prefix) return false;
            if ($sLen > 0 && substr($part, -$sLen) !== $suffix) return false;
        }
        return true;
    }

    private function collapseToRanges(array $chars): string
    {
        $result = '';
        $i = 0;
        $n = count($chars);
        sort($chars);

        while ($i < $n) {
            $start = $chars[$i];
            $j = $i;
            while ($j + 1 < $n && ord($chars[$j + 1]) - ord($chars[$j]) === 1) {
                $j++;
            }
            if ($j - $i >= 2) {
                $result .= $start . '-' . $chars[$j];
                $i = $j + 1;
            } elseif ($j - $i === 1) {
                $result .= $start . $chars[$j];
                $i = $j + 1;
            } else {
                $result .= $start;
                $i++;
            }
        }
        return $result;
    }
}
// === CLI ===
$optimizer = new RegexOptimizer();

while (true) {
    echo "> ";
    $input = trim(fgets(STDIN));

    if ($input === 'exit' || $input === 'quit') {
        echo "👋 bay!\n";
        break;
    }

    if ($input === '') continue;

    try {
        $optimized = $optimizer->optimize($input);

        if ($optimized === $input) {
            echo "ℹ️  it was not possible to optimize:\n   $input\n";
        } else {
            $saved = strlen($input) - strlen($optimized);
            echo "✅ optimized:\n";
            echo "   be: $input\n";
            echo "   so: $optimized\n";
            echo "   economy: $saved symbols (" . round($saved * 100 / strlen($input)) . "%)\n";
        }
        echo "\n";
    } catch (Throwable $e) {
        echo "❌ error: " . $e->getMessage() . "\n\n";
    }
}