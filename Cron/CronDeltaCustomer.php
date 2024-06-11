<?php

namespace Innovadeltech\Convertopia\Cron;

use Psr\Log\LoggerInterface;

/**
 * CronDelta class run cron functionality executes
 */
class CronDeltaCustomer
{

    /**
     * @var $logger
     */
    protected $logger;
     /**
      * Get constructor
      *
      * @var constructor
      *
      * @param var $exportCustomerFactory
      * @param var $exportOrderFactory
      * @param var $exportProductFactory
      */
    public function __construct(
        \Innovadeltech\Convertopia\Model\Customers\ExportFactory $exportCustomerFactory,
        \Innovadeltech\Convertopia\Model\Orders\ExportFactory $exportOrderFactory,
        \Innovadeltech\Convertopia\Model\Products\ExportFactory $exportProductFactory
    ) {
        $this->exportCustomerFactory = $exportCustomerFactory;
        $this->exportOrderFactory = $exportOrderFactory;
        $this->exportProductFactory = $exportProductFactory;
    }

     /**
      * Get execute
      *
      * @var execute
      */
    public function execute()
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/convertopia.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info("Delta schduled convertopia new");
        $isDelta = true;
        $this->exportCustomerFactory->create()->getExport($isDelta);
        // $this->exportCustomerFactory->getExport();
        // add your custom code as per your requirement
    }
}
