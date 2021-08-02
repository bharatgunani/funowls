<?php
namespace RedChamps\GuestOrders\Model\System\Config\Backend;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use RedChamps\Core\Api\TimeStampRepositoryInterface;

/*
 * Package: GuestOrders
 * Class: ProcessingMethod
 * Company: RedChamps
 * author: rav(rav@redchamps.com)
 * */
class ProcessingMethod extends Value
{
    protected $configWriter;

    protected $orderCollectionFactory;
    /**
     * @var TimeStampRepositoryInterface
     */
    private $timeStampRepository;

    /**
     * ProcessingMethod constructor.
     * @param TimeStampRepositoryInterface $timeStampRepository
     * @param CollectionFactory $orderCollectionFactory
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        TimeStampRepositoryInterface $timeStampRepository,
        CollectionFactory $orderCollectionFactory,
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->timeStampRepository = $timeStampRepository;
    }

    /**
     * @return Value
     */
    public function beforeSave()
    {
        if ($this->isValueChanged() && $this->getValue() == "cron") {
            $orderCollection = $this->orderCollectionFactory->create()
                ->setOrder('entity_id', 'DESC');
            $orderCollection->getSelect()->limit(1);
            $order = $orderCollection->getFirstItem();
            if ($order && $order->getId()) {
                $lastProcessedTime = $order->getCreatedAt();
                $this->timeStampRepository->save(
                    $this->timeStampRepository->getByType("guest_orders")
                        ->setLastProcessed($lastProcessedTime)
                );
            }
        }
        return parent::beforeSave();
    }
}
