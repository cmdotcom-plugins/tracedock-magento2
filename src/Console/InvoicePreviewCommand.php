<?php

namespace Tracedock\TransactionTracking\Console;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Model\Order\InvoiceRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracedock\TransactionTracking\Model\Mapper;

class InvoicePreviewCommand extends Command
{
    public const COMMAND_PREVIEW_INVOICE = 'tracedock:preview:invoice';
    public const INVOICE_ARGUMENT        = 'invoice';

    /**
     * @var State
     */
    private State $appState;

    /**
     * @var InvoiceRepository
     */
    private InvoiceRepository $invoiceRepository;

    /**
     * @var Mapper
     */
    private Mapper $mapper;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * InvoicePreviewCommand constructor.
     * @param State $appState
     * @param InvoiceRepository $invoiceRepository
     * @param Mapper $mapper
     * @param SerializerInterface $serializer
     */
    public function __construct(
        State $appState,
        InvoiceRepository $invoiceRepository,
        Mapper $mapper,
        SerializerInterface $serializer
    ) {
        $this->appState = $appState;
        $this->invoiceRepository = $invoiceRepository;
        $this->mapper = $mapper;
        $this->serializer = $serializer;

        parent::__construct(self::COMMAND_PREVIEW_INVOICE);
    }

    /**
     * Execute Preview Invoice Console Command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $entityId = (int)$input->getArgument(self::INVOICE_ARGUMENT);

        $this->setAreaCode();

        try {
            $invoice = $this->invoiceRepository->get($entityId);
            $output->writeln(
                $this->serializer->serialize(
                    $this->mapper->map($invoice)
                )
            );
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return 500;
        }

        return 0;
    }

    /**
     * Configure Preview Tracedock Invoice Console Command
     */
    protected function configure(): void
    {
        $this->setName(self::COMMAND_PREVIEW_INVOICE);
        $this->setDescription('Preview an invoice payload for TraceDock tracking. For debugging purposes.');
        $this->setDefinition(
            [
                new InputArgument(
                    self::INVOICE_ARGUMENT,
                    InputArgument::REQUIRED,
                    'Invoice Entity Id'
                )
            ]
        );

        parent::configure();
    }

    /**
     * Set Area Code since it's needed for the config value's being fetched for the invoice entity's.
     *
     * @throws LocalizedException
     */
    private function setAreaCode()
    {
        try {
            $this->appState->getAreaCode();
        } catch (LocalizedException $e) {
            $this->appState->setAreaCode(Area::AREA_ADMINHTML);
        }
    }
}
