<?php

namespace Google\AdsApi\AdWords\v201802\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class AdGroupBidModifierOperation extends \Google\AdsApi\AdWords\v201802\cm\Operation
{
    /**
     * @var \Google\AdsApi\AdWords\v201802\cm\AdGroupBidModifier $operand
     */
    protected $operand = null;
    /**
     * @param string $operator
     * @param string $OperationType
     * @param \Google\AdsApi\AdWords\v201802\cm\AdGroupBidModifier $operand
     */
    public function __construct($operator = null, $OperationType = null, $operand = null)
    {
        parent::__construct($operator, $OperationType);
        $this->operand = $operand;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201802\cm\AdGroupBidModifier
     */
    public function getOperand()
    {
        return $this->operand;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201802\cm\AdGroupBidModifier $operand
     * @return \Google\AdsApi\AdWords\v201802\cm\AdGroupBidModifierOperation
     */
    public function setOperand($operand)
    {
        $this->operand = $operand;
        return $this;
    }
}