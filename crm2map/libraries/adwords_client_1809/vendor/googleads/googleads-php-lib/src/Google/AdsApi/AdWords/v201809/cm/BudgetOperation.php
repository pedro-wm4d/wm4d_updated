<?php

namespace Google\AdsApi\AdWords\v201809\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class BudgetOperation extends \Google\AdsApi\AdWords\v201809\cm\Operation
{
    /**
     * @var \Google\AdsApi\AdWords\v201809\cm\Budget $operand
     */
    protected $operand = null;
    /**
     * @param string $operator
     * @param string $OperationType
     * @param \Google\AdsApi\AdWords\v201809\cm\Budget $operand
     */
    public function __construct($operator = null, $OperationType = null, $operand = null)
    {
        parent::__construct($operator, $OperationType);
        $this->operand = $operand;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201809\cm\Budget
     */
    public function getOperand()
    {
        return $this->operand;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201809\cm\Budget $operand
     * @return \Google\AdsApi\AdWords\v201809\cm\BudgetOperation
     */
    public function setOperand($operand)
    {
        $this->operand = $operand;
        return $this;
    }
}