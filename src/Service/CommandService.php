<?php

namespace App\Service;

class CommandService
{
    /**
     * @return array<mixed>
     */
    public function run(string $command)
    {
        $command .= ' 2>&1';

        exec($command, $output, $status);

        return [
            'output' => $output,
            'status' => $status,
        ];
    }
}
