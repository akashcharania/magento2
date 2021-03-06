<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Model\Resource\Order\Status;

use Magento\Sales\Model\Order\Status\History\Validator;
use Magento\Sales\Model\Resource\EntityAbstract;
use Magento\Sales\Model\Resource\EntitySnapshot;
use Magento\Sales\Model\Spi\OrderStatusHistoryResourceInterface;

/**
 * Flat sales order status history resource
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class History extends EntityAbstract implements OrderStatusHistoryResourceInterface
{
    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @param \Magento\Framework\Model\Resource\Db\Context $context
     * @param \Magento\Sales\Model\Resource\Attribute $attribute
     * @param \Magento\SalesSequence\Model\Manager $sequenceManager
     * @param EntitySnapshot $entitySnapshot
     * @param \Magento\Sales\Model\Resource\EntityRelationComposite $entityRelationComposite
     * @param Validator $validator
     * @param string $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\Resource\Db\Context $context,
        \Magento\Sales\Model\Resource\Attribute $attribute,
        \Magento\SalesSequence\Model\Manager $sequenceManager,
        EntitySnapshot $entitySnapshot,
        \Magento\Sales\Model\Resource\EntityRelationComposite $entityRelationComposite,
        Validator $validator,
        $resourcePrefix = null
    ) {
        $this->validator = $validator;
        parent::__construct(
            $context,
            $attribute,
            $sequenceManager,
            $entitySnapshot,
            $entityRelationComposite,
            $resourcePrefix
        );
    }

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sales_order_status_history_resource';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_order_status_history', 'entity_id');
    }

    /**
     * Perform actions before object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::_beforeSave($object);
        $warnings = $this->validator->validate($object);
        if (!empty($warnings)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("Cannot save comment:\n%1", implode("\n", $warnings))
            );
        }
        return $this;
    }
}
