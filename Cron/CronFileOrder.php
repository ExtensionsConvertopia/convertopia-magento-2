<?php

namespace Innovadeltech\Convertopia\Cron;

use Psr\Log\LoggerInterface;

class CronFileOrder
{
    /**
     * @var logger
     */
    protected $logger;
    /**
     * Get __construct
     *
     * @var __construct
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
        $logger->info("scduled convertopia new");
        $isDelta=false;
        $this->exportOrderFactory->create()->getExport($isDelta);
 
        // $this->exportCustomerFactory->getExport();
        // add your custom code as per your requirement
    }
}
