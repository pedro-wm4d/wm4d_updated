<?php

namespace Google\AdsApi\AdWords\v201809\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class CampaignGroupPerformanceTargetPage extends \Google\AdsApi\AdWords\v201809\cm\Page
{
    /**
     * @var \Google\AdsApi\AdWords\v201809\cm\CampaignGroupPerformanceTarget[] $entries
     */
    protected $entries = null;
    /**
     * @param int $totalNumEntries
     * @param string $PageType
     * @param \Google\AdsApi\AdWords\v201809\cm\CampaignGroupPerformanceTarget[] $entries
     */
    public function __construct($totalNumEntries = null, $PageType = null, array $entries = null)
    {
        parent::__construct($totalNumEntries, $PageType);
        $this->entries = $entries;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201809\cm\CampaignGroupPerformanceTarget[]
     */
    public function getEntries()
    {
        return $this->entries;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201809\cm\CampaignGroupPerformanceTarget[] $entries
     * @return \Google\AdsApi\AdWords\v201809\cm\CampaignGroupPerformanceTargetPage
     */
    public function setEntries(array $entries)
    {
        $this->entries = $entries;
        return $this;
    }
}