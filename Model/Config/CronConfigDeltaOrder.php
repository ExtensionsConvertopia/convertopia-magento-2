<?php

namespace Innovadeltech\Convertopia\Model\Config;

class CronConfigDeltaOrder extends \Magento\Framework\App\Config\Value
{
    public const CRON_STRING_PATH = 'crontab/default/jobs/convertopia_order_delta/schedule/cron_expr';

    public const CRON_MODEL_PATH = 'crontab/default/jobs/convertopia_order_delta/run/model';
     /**
      * @var _configValueFactory
      */
    protected $_configValueFactory;

     /**
      * @var _runModelPath
      */
    protected $_runModelPath = '';

/**
 * Saves Cron Data
 *
 * @param \Magento\Framework\Model\Context $context
 * @param \Magento\Framework\Registry $registry
 * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
 * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
 * @param \Magento\Framework\App\Config\ValueFactory $configValueFactory
 * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
 * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
 * @param int $runModelPath
 * @param array $data
 */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        $runModelPath = '',
        array $data = []
    ) {
        $this->_runModelPath = $runModelPath;
        $this->_configValueFactory = $configValueFactory;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Saves Cron Data
     *
     * @param afterSave
     */
    public function afterSave()
    {
        $time = $this->getData('groups/cronScheduledDelta/groups/orderScheduled/fields/time/value');
        $frequency = $this->getData('groups/cronScheduledDelta/groups/orderScheduled/fields/frequency/value');

        $cronExprArray = [
            (int)($time[1]),
            (int)($time[0]),
            $frequency == \Magento\Cron\Model\Config\Source\Frequency::CRON_MONTHLY ? '1' : '*',
            '*',
            $frequency == \Magento\Cron\Model\Config\Source\Frequency::CRON_WEEKLY ? '1' : '*',
        ];

        $cronExprString = join(' ', $cronExprArray);

        try {

            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/scdeuled_cron.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info("sss");
            $this->_configValueFactory->create()->load(
                self::CRON_STRING_PATH,
                'path'
            )->setValue(
                $cronExprString
            )->setPath(
                self::CRON_STRING_PATH
            )->save();
            $this->_configValueFactory->create()->load(
                self::CRON_MODEL_PATH,
                'path'
            )->setValue(
                $this->_runModelPath
            )->setPath(
                self::CRON_MODEL_PATH
            )->save();
        } catch (\Exception $e) {
            throw new CronExpressionException(__('We can\'t save the cron expression.'), 0, $e);
        }

        return parent::afterSave();
    }
}
