<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesSequence\Model;

use Magento\Framework\App\Resource as AppResource;
use Magento\Framework\DB\Sequence\SequenceInterface;

/**
 * Class Sequence represents sequence in logic
 */
class Sequence implements SequenceInterface
{
    /**
     * Default pattern for Sequence
     */
    const DEFAULT_PATTERN  = "%s%'.09d%s";

    /**
     * @var string
     */
    private $lastIncrementId;

    /**
     * @var Meta
     */
    private $meta;

    /**
     * @var false|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $adapter;

    /**
     * @var string
     */
    private $pattern;

    /**
     * @param Meta $meta
     * @param AppResource $resource
     * @param string $pattern
     */
    public function __construct(
        Meta $meta,
        AppResource $resource,
        $pattern = self::DEFAULT_PATTERN
    ) {
        $this->meta = $meta;
        $this->adapter = $resource->getConnection('sales_write');
        $this->pattern = $pattern;
    }

    /**
     * Retrieve current value
     *
     * @return string
     */
    public function getCurrentValue()
    {
        if (!isset($this->lastIncrementId)) {
            return null;
        }

        return sprintf(
            $this->pattern,
            $this->meta->getActiveProfile()->getPrefix(),
            $this->calculateCurrentValue(),
            $this->meta->getActiveProfile()->getSuffix()
        );
    }

    /**
     * Retrieve next value
     *
     * @return string
     */
    public function getNextValue()
    {
        $this->adapter->insert($this->meta->getSequenceTable(), []);
        $this->lastIncrementId = $this->adapter->lastInsertId($this->meta->getSequenceTable());
        return $this->getCurrentValue();
    }

    /**
     * Calculate current value depends on start value
     *
     * @return string
     */
    private function calculateCurrentValue()
    {
        return ($this->lastIncrementId - $this->meta->getActiveProfile()->getStartValue())
        * $this->meta->getActiveProfile()->getStep() + $this->meta->getActiveProfile()->getStartValue();
    }
}
