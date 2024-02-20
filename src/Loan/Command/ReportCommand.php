<?php

declare(strict_types=1);

namespace App\Loan\Command;

use App\Loan\Service\Payment\Report\PaymentReportGenerator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'report',
    description: 'Show payments by date',
)]
final class ReportCommand extends Command
{
    private const string DATE_OPTION = 'date';

    public function __construct(
        private readonly PaymentReportGenerator $reportGenerator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(self::DATE_OPTION, null, InputOption::VALUE_REQUIRED, 'date in format YYYY-MM-DD')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = (string) $input->getOption(self::DATE_OPTION);
        $paymentDate = \DateTimeImmutable::createFromFormat('Y-m-d', $date);

        if (false === $paymentDate) {
            return self::INVALID;
        }

        $table = new Table($output);

        $table->setHeaders(['Id', 'Status', 'PayerName', 'PayerSurname', 'Amount', 'RefId']);

        foreach ($this->reportGenerator->generateByPaymentDate($paymentDate) as $paymentModel) {
            $table->addRow([
                $paymentModel->id,
                $paymentModel->status,
                $paymentModel->payerName,
                $paymentModel->payerSurname,
                $paymentModel->amount,
                $paymentModel->refId,
            ]);
        }

        $table->render();

        return Command::SUCCESS;
    }
}
