<?php

namespace App\Command;

use App\Traits\HasCommandService;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class DatabaseDumpCommand extends Command
{
    use HasCommandService;

    public function configure(): void
    {
        $this
            ->setName('app:database:dump')
            ->setDescription('Dumps database');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $path       = $_ENV['DATABASE_DUMP_PATH'];
        $filesystem = new Filesystem();

        $output->writeln(sprintf(
            '<comment>Dumping <fg=green>%s</fg=green> to <fg=green>%s</fg=green></comment>',
            'campen',
            $path
        ));

        if (!$filesystem->exists($path)) {
            $filesystem->mkdir(dirname($path));
        }

        $backupResult = $this->commandService->run(sprintf(
            'mysqldump -B %s -u %s --password=%s',
            $_ENV['DATABASE_NAME'],
            $_ENV['DATABASE_USER'],
            $_ENV['DATABASE_PASSWORD']
        ));

        if ($backupResult['status'] > 0) {
            throw new Exception('Error dumping database: ' . var_export($backupResult['output'], true));
        }

        $filesystem->dumpFile($path, implode(PHP_EOL, $backupResult['output']));

        $encryptionResult = $this->commandService->run(sprintf(
            'openssl enc -aes-256-cbc -pass pass:%s -in %s -out %s',
            $_ENV['DATABASE_DUMP_KEY'],
            $path,
            $path . '.enc',
        ));

        if ($encryptionResult['status'] > 0) {
            throw new Exception('Error encrypting database: ' . var_export($encryptionResult['output'], true));
        }

        $filesystem->remove($path);

        $output->writeln('
            <comment><fg=green>Dump successfully created</fg=green></comment>
        ');

        return 0;
    }
}
