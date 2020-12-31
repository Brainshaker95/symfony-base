<?php

namespace App\Command;

use App\Traits\HasCommandService;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class DatabaseRestoreCommand extends Command
{
    use HasCommandService;

    public function configure(): void
    {
        $this
            ->setName('app:database:restore')
            ->setDescription('Restores database');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $path       = $_ENV['DATABASE_DUMP_PATH'];
        $filesystem = new Filesystem();

        $output->writeln(sprintf(
            '<comment>Restoring dump <fg=green>%s</fg=green> from <fg=green>%s</fg=green></comment>',
            'campen',
            $path
        ));

        if (!$filesystem->exists($path . '.enc')) {
            throw new Exception('Error restoring database: Dump file not found at ' . $path . '.enc');
        }

        $decryptionResult = $this->commandService->run(sprintf(
            'openssl enc -aes-256-cbc -d -pass pass:%s -in %s > %s',
            $_ENV['DATABASE_DUMP_KEY'],
            $path . '.enc',
            $path,
        ));

        if ($decryptionResult['status'] > 0) {
            throw new Exception('Error derypting database: ' . var_export($decryptionResult['output'], true));
        }

        $this->commandService->run(sprintf(
            'mysql -B %s -u %s --password=%s < %s',
            $_ENV['DATABASE_NAME'],
            $_ENV['DATABASE_USER'],
            $_ENV['DATABASE_PASSWORD'],
            $path
        ));

        $filesystem->remove($path);

        $output->writeln('
            <comment><fg=green>Dump successfully restored</fg=green></comment>
        ');

        return 0;
    }
}
