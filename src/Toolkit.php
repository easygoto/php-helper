<?php

namespace Trink\Core\Helper;

use Throwable;

class Toolkit
{
    /**
     * 异常转数组
     *
     * @param Throwable $throwable
     * @param array     $options traceDeep(堆栈追踪的深度, 'all' 为全部追踪)
     *
     * @return array
     */
    public static function throwable2Array(Throwable $throwable, array $options = ['traceDeep' => 0]): array
    {
        $traceDeep = $options['traceDeep'] ?? 0;
        $trace = ($traceDeep === 'all') ? $throwable->getTrace() : array_slice($throwable->getTrace(), 0, $traceDeep);

        return [
            'code' => $throwable->getCode(),
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'message' => $throwable->getMessage(),
            'trace' => $trace,
        ];
    }
}
