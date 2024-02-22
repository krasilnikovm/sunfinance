<?php

declare(strict_types=1);

namespace App\Loan\Command;

use App\Loan\Service\Payment\Exception\DuplicatePaymentException;
use App\Loan\Service\Payment\Exception\LoanNotFoundException;
use App\Loan\Service\Payment\Exception\ValidationException;
use App\Loan\Service\Payment\PaymentImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'import',
    description: 'Import csv file with payments',
)]
final class ImportCommand extends Command
{
    private const string FILE_OPTION = 'file';

    public function __construct(
        private readonly PaymentImporter $csvImporter
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(self::FILE_OPTION, null, InputOption::VALUE_REQUIRED, 'Path to csv file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = (string) $input->getOption(self::FILE_OPTION);

        if (empty($filePath)) {
            $output->writeln('<error>Empty Path</error>');

            return self::INVALID;
        }

        try {
            $this->csvImporter->import($filePath);
        } catch (DuplicatePaymentException $exception) {
            $output->writeln("<error>{$exception->getMessage()}</error>");

            return self::FAILURE;
        } catch (ValidationException $exception) {
            $errors = implode(', ', $exception->errors);
            $output->writeln("<error>$errors</error>");

            return self::INVALID;
        } catch (LoanNotFoundException $exception) {
            $output->writeln("<error>{$exception->getMessage()}</error>");

            return self::INVALID;
        }

        return Command::SUCCESS;
    }
}
