<?php

namespace App\Application\DataMigration\Services;

class SqlDumpSplitter
{
    /**
     * @return array<int, string>
     */
    public function split(string $sql): array
    {
        $statements = [];
        $buffer = '';
        $length = strlen($sql);
        $state = 'normal';

        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];
            $next = $i + 1 < $length ? $sql[$i + 1] : '';

            if ($state === 'single') {
                $buffer .= $char;

                if ($char === '\\' && $next !== '') {
                    $buffer .= $next;
                    $i++;
                    continue;
                }

                if ($char === "'" && $next === "'") {
                    $buffer .= $next;
                    $i++;
                    continue;
                }

                if ($char === "'") {
                    $state = 'normal';
                }

                continue;
            }

            if ($state === 'double') {
                $buffer .= $char;

                if ($char === '\\' && $next !== '') {
                    $buffer .= $next;
                    $i++;
                    continue;
                }

                if ($char === '"' && $next === '"') {
                    $buffer .= $next;
                    $i++;
                    continue;
                }

                if ($char === '"') {
                    $state = 'normal';
                }

                continue;
            }

            if ($state === 'identifier') {
                $buffer .= $char;

                if ($char === '`') {
                    $state = 'normal';
                }

                continue;
            }

            if ($state === 'line-comment') {
                $buffer .= $char;

                if ($char === "\n") {
                    $state = 'normal';
                }

                continue;
            }

            if ($state === 'block-comment') {
                $buffer .= $char;

                if ($char === '*' && $next === '/') {
                    $buffer .= $next;
                    $i++;
                    $state = 'normal';
                }

                continue;
            }

            if ($char === "'") {
                $buffer .= $char;
                $state = 'single';
                continue;
            }

            if ($char === '"') {
                $buffer .= $char;
                $state = 'double';
                continue;
            }

            if ($char === '`') {
                $buffer .= $char;
                $state = 'identifier';
                continue;
            }

            if ($char === '-' && $next === '-' && $this->isLineCommentStart($sql, $i)) {
                $buffer .= $char.$next;
                $i++;
                $state = 'line-comment';
                continue;
            }

            if ($char === '#') {
                $buffer .= $char;
                $state = 'line-comment';
                continue;
            }

            if ($char === '/' && $next === '*') {
                $buffer .= $char.$next;
                $i++;
                $state = 'block-comment';
                continue;
            }

            if ($char === ';') {
                $this->pushStatement($statements, $buffer);
                $buffer = '';
                continue;
            }

            $buffer .= $char;
        }

        $this->pushStatement($statements, $buffer);

        return $statements;
    }

    private function isLineCommentStart(string $sql, int $offset): bool
    {
        $after = $offset + 2 < strlen($sql) ? $sql[$offset + 2] : '';

        return $after === '' || ctype_space($after);
    }

    /**
     * @param  array<int, string>  $statements
     */
    private function pushStatement(array &$statements, string $statement): void
    {
        $statement = trim($statement);

        if ($statement === '' || $this->stripComments($statement) === '') {
            return;
        }

        $statements[] = $statement;
    }

    private function stripComments(string $statement): string
    {
        do {
            $before = $statement;
            $statement = trim($statement);

            if (str_starts_with($statement, '--')) {
                $position = strpos($statement, "\n");
                $statement = $position === false ? '' : substr($statement, $position + 1);
                continue;
            }

            if (str_starts_with($statement, '#')) {
                $position = strpos($statement, "\n");
                $statement = $position === false ? '' : substr($statement, $position + 1);
                continue;
            }

            if (str_starts_with($statement, '/*')) {
                $position = strpos($statement, '*/');
                $statement = $position === false ? '' : substr($statement, $position + 2);
                continue;
            }
        } while ($before !== $statement);

        return trim($statement);
    }
}
