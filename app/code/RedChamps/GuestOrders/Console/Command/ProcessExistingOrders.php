<?php
namespace RedChamps\GuestOrders\Console\Command;

use Magento\Customer\Model\ResourceModel\Customer\Collection;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use RedChamps\GuestOrders\Model\Processor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessExistingOrders extends Command
{
    const NAME = 'name';

    /**
     * @var Collection
     */
    private $customerCollection;

    /**
     * @var Processor
     */
    private $processor;
    /**
     * @var OrderCollection
     */
    private $orderCollection;

    public function __construct(
        Collection $customerCollection,
        OrderCollection $orderCollection,
        Processor $processor,
        string $name = null
    ) {
        parent::__construct($name);
        $this->customerCollection = $customerCollection;
        $this->processor = $processor;
        $this->orderCollection = $orderCollection;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('redChamps:guestOrders:process-existing-orders');
        $this->setDescription('Process assignment of existing Guest Orders.');
        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return null|int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if($this->guestOrderExist()) {
            foreach ($this->customerCollection as $customer) {
                $output->writeln("<comment>Processing orders of customer {$customer->getEmail()}</comment>");
                $result = $this->processor->assignOrdersToCustomer($customer->getEmail(), $customer);
                if(is_array($result)) {
                    $count = count($result);
                    $output->writeln("<comment>->Processed $count orders.</comment>");
                } else {
                    $output->writeln("<error>->Error: $result</error>");
                }
            }
            $output->writeln('<info>Done :-)</info>');
        } else {
            $output->writeln('<info>No guest order(s) found. They might be already processed.</info>');
        }
    }

    protected function guestOrderExist()
    {
        return $this->orderCollection->addFieldToFilter('customer_is_guest', 1)
            ->addFieldToFilter('customer_id', ['null' => true])
        ->count();
    }
}
