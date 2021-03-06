<?php

namespace Google\AdsApi\AdWords\v201809\rm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class DateSpecificRuleUserList extends \Google\AdsApi\AdWords\v201809\rm\RuleBasedUserList
{
    /**
     * @var \Google\AdsApi\AdWords\v201809\rm\Rule $rule
     */
    protected $rule = null;
    /**
     * @var string $startDate
     */
    protected $startDate = null;
    /**
     * @var string $endDate
     */
    protected $endDate = null;
    /**
     * @param int $id
     * @param boolean $isReadOnly
     * @param string $name
     * @param string $description
     * @param string $status
     * @param string $integrationCode
     * @param string $accessReason
     * @param string $accountUserListStatus
     * @param int $membershipLifeSpan
     * @param int $size
     * @param string $sizeRange
     * @param int $sizeForSearch
     * @param string $sizeRangeForSearch
     * @param string $listType
     * @param boolean $isEligibleForSearch
     * @param boolean $isEligibleForDisplay
     * @param string $closingReason
     * @param string $UserListType
     * @param string $prepopulationStatus
     * @param \Google\AdsApi\AdWords\v201809\rm\Rule $rule
     * @param string $startDate
     * @param string $endDate
     */
    public function __construct($id = null, $isReadOnly = null, $name = null, $description = null, $status = null, $integrationCode = null, $accessReason = null, $accountUserListStatus = null, $membershipLifeSpan = null, $size = null, $sizeRange = null, $sizeForSearch = null, $sizeRangeForSearch = null, $listType = null, $isEligibleForSearch = null, $isEligibleForDisplay = null, $closingReason = null, $UserListType = null, $prepopulationStatus = null, $rule = null, $startDate = null, $endDate = null)
    {
        parent::__construct($id, $isReadOnly, $name, $description, $status, $integrationCode, $accessReason, $accountUserListStatus, $membershipLifeSpan, $size, $sizeRange, $sizeForSearch, $sizeRangeForSearch, $listType, $isEligibleForSearch, $isEligibleForDisplay, $closingReason, $UserListType, $prepopulationStatus);
        $this->rule = $rule;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201809\rm\Rule
     */
    public function getRule()
    {
        return $this->rule;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201809\rm\Rule $rule
     * @return \Google\AdsApi\AdWords\v201809\rm\DateSpecificRuleUserList
     */
    public function setRule($rule)
    {
        $this->rule = $rule;
        return $this;
    }
    /**
     * @return string
     */
    public function getStartDate()
    {
        return $this->startDate;
    }
    /**
     * @param string $startDate
     * @return \Google\AdsApi\AdWords\v201809\rm\DateSpecificRuleUserList
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }
    /**
     * @return string
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
    /**
     * @param string $endDate
     * @return \Google\AdsApi\AdWords\v201809\rm\DateSpecificRuleUserList
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }
}