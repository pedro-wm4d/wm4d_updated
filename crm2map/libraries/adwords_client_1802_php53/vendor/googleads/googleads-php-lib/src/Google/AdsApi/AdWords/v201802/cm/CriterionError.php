<?php

namespace Google\AdsApi\AdWords\v201802\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class CriterionError extends \Google\AdsApi\AdWords\v201802\cm\ApiError
{
    /**
     * @var string $reason
     */
    protected $reason = null;
    /**
     * @param string $fieldPath
     * @param \Google\AdsApi\AdWords\v201802\cm\FieldPathElement[] $fieldPathElements
     * @param string $trigger
     * @param string $errorString
     * @param string $ApiErrorType
     * @param string $reason
     */
    public function __construct($fieldPath = null, array $fieldPathElements = null, $trigger = null, $errorString = null, $ApiErrorType = null, $reason = null)
    {
        parent::__construct($fieldPath, $fieldPathElements, $trigger, $errorString, $ApiErrorType);
        $this->reason = $reason;
    }
    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }
    /**
     * @param string $reason
     * @return \Google\AdsApi\AdWords\v201802\cm\CriterionError
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
        return $this;
    }
}