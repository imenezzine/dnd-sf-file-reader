<?php

declare(strict_types=1);

namespace App\Command;

use App\File\FileReaderInterface;
use App\Output\JsonFormatter;
use App\Output\TableFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DisplayDataFromCSVFileCommand extends Command
{
    /**
     * @var FileReaderInterface
     */
    private $fileReader;

    /**
     * @var JsonFormatter
     */
    private $jsonFormatter;

    /**
     * @var TableFormatter
     */
    private $tableFormatter;

    public function __construct(FileReaderInterface $fileReader, JsonFormatter $jsonFormatter, TableFormatter $tableFormatter, string $name = null)
    {
        parent::__construct($name);

        $this->fileReader = $fileReader;
        $this->jsonFormatter = $jsonFormatter;
        $this->tableFormatter = $tableFormatter;
    }

    protected static $defaultName = 'app:display-data-csv-file';

    protected function configure()
    {
        $this
            ->setDescription('Display data from csv file with a specific format')
            ->addArgument('path', InputArgument::REQUIRED, 'path of file')
            ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'display format');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $path = $input->getArgument('path');
        $format = $input->getOption('format');
        $rows = $this->fileReader->getLines($path);

        if ('json' == $format) {
            $io->text($this->jsonFormatter->render($this->fileReader->format($rows)));
        } else {
            $this->tableFormatter->render($output, $this->fileReader->format($rows));
        }

        return 0;
    }
}
