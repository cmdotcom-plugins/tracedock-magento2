<?php

declare(strict_types=1);

namespace Tracedock\TransactionTracking\Console;

use DomainException;
use Tracedock\TransactionTracking\Api\PublisherInterface;
use Magento\Sales\Model\Order\InvoiceRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InvoicePublishCommand extends Command
{
    public const COMMAND_QUEUE_MESSAGE_PUBLISH = 'tracedock:queue:invoice';
    public const MESSAGE_ARGUMENT              = 'message';
    public const TOPIC_ARGUMENT                = 'topic';

    private PublisherInterface $publisher;

    private InvoiceRepository $invoiceRepository;

    public function __construct(
        PublisherInterface $publisher,
        InvoiceRepository $invoiceRepository
    ) {
        $this->publisher         = $publisher;
        $this->invoiceRepository = $invoiceRepository;
        parent::__construct(self::COMMAND_QUEUE_MESSAGE_PUBLISH);
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $entityId = (int)$input->getArgument(self::MESSAGE_ARGUMENT);

        try {
            $invoice = $this->invoiceRepository->get($entityId);

            $this->publisher->publish($invoice, true);
            $output->writeln(
                sprintf(
                    'Published invoice "%s" for TraceDock Tracking',
                    $entityId,
                )
            );
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return 500;
        }

        return 0;
    }

    protected function configure(): void
    {
        $this->setName(self::COMMAND_QUEUE_MESSAGE_PUBLISH);
        $this->setDescription('Publish an invoice for TraceDock tracking');
        $this->setDefinition(
            [
                new InputArgument(
                    self::MESSAGE_ARGUMENT,
                    InputArgument::REQUIRED,
                    'Invoice Entity Id'
                )
            ]
        );

        parent::configure();
    }
}
