<?php

declare(strict_types=1);

function scraplingBridgeRun(string $mode, array $params = [], int $timeoutSeconds = 60): ?array
{
    if (getenv('SCRAPLING_DISABLED') === '1') {
        return null;
    }

    if (!function_exists('proc_open')) {
        error_log('Scrapling bridge skipped: proc_open is disabled.');
        return null;
    }

    $scriptPath = __DIR__ . DIRECTORY_SEPARATOR . 'scrapling-vod-bridge.py';

    if (!is_file($scriptPath)) {
        error_log('Scrapling bridge skipped: helper script is missing.');
        return null;
    }

    $pythonBinary = getenv('SCRAPLING_PYTHON');

    if (!is_string($pythonBinary) || trim($pythonBinary) === '') {
        $pythonBinary = PHP_OS_FAMILY === 'Windows' ? 'python' : 'python3';
    }

    $command = [
        $pythonBinary,
        $scriptPath,
        $mode,
    ];

    foreach ($params as $key => $value) {
        if (!is_scalar($value) && $value !== null) {
            continue;
        }

        $command[] = '--' . preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $key);
        $command[] = (string) $value;
    }

    $commandLine = implode(' ', array_map('escapeshellarg', $command));
    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = @proc_open($commandLine, $descriptors, $pipes, __DIR__);

    if (!is_resource($process)) {
        error_log('Scrapling bridge skipped: process could not be started.');
        return null;
    }

    fclose($pipes[0]);
    stream_set_blocking($pipes[1], false);
    stream_set_blocking($pipes[2], false);

    $startedAt = microtime(true);
    $exitCode = null;
    $stdout = '';
    $stderr = '';

    while (true) {
        $stdout .= stream_get_contents($pipes[1]) ?: '';
        $stderr .= stream_get_contents($pipes[2]) ?: '';

        $status = proc_get_status($process);

        if (!$status['running']) {
            $exitCode = is_int($status['exitcode']) ? $status['exitcode'] : null;
            break;
        }

        if ((microtime(true) - $startedAt) > $timeoutSeconds) {
            proc_terminate($process);

            foreach ([1, 2] as $pipeIndex) {
                if (isset($pipes[$pipeIndex]) && is_resource($pipes[$pipeIndex])) {
                    fclose($pipes[$pipeIndex]);
                }
            }

            proc_close($process);
            error_log('Scrapling bridge timed out for mode: ' . $mode);
            return null;
        }

        usleep(100000);
    }

    $stdout .= stream_get_contents($pipes[1]) ?: '';
    $stderr .= stream_get_contents($pipes[2]) ?: '';

    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($process);

    if ($exitCode !== 0) {
        $decodedError = json_decode((string) $stdout, true);
        $message = is_array($decodedError) && is_string($decodedError['error'] ?? null)
            ? $decodedError['error']
            : trim((string) $stderr);

        error_log('Scrapling bridge failed: ' . $message);
        return null;
    }

    $decoded = json_decode((string) $stdout, true);

    if (!is_array($decoded)) {
        error_log('Scrapling bridge returned invalid JSON.');
        return null;
    }

    if (empty($decoded['ok']) || !is_array($decoded['payload'] ?? null)) {
        $error = is_string($decoded['error'] ?? null) ? $decoded['error'] : 'unknown error';
        error_log('Scrapling bridge returned no payload: ' . $error);
        return null;
    }

    return $decoded['payload'];
}
