<?php

namespace Google\AdsApi\AdWords\v201809\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class CampaignSharedSetOperation extends \Google\AdsApi\AdWords\v201809\cm\Operation
{
    /**
     * @var \Google\AdsApi\AdWords\v201809\cm\CampaignSharedSet $operand
     */
    protected $operand = null;
    /**
     * @param string $operator
     * @param string $OperationType
     * @param \Google\AdsApi\AdWords\v201809\cm\CampaignSharedSet $operand
     */
    public function __construct($operator = null, $OperationType = null, $operand = null)
    {
        parent::__construct($operator, $OperationType);
        $this->operand = $operand;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201809\cm\CampaignSharedSet
     */
    public function getOperand()
    {
        return $this->operand;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201809\cm\CampaignSharedSet $operand
     * @return \Google\AdsApi\AdWords\v201809\cm\CampaignSharedSetOperation
     */
    public function setOperand($operand)
    {
        $this->operand = $operand;
        return $this;
    }
}