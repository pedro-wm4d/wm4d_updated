<?php

namespace Google\AdsApi\AdWords\v201802\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class FeedPage extends \Google\AdsApi\AdWords\v201802\cm\NullStatsPage
{
    /**
     * @var \Google\AdsApi\AdWords\v201802\cm\Feed[] $entries
     */
    protected $entries = null;
    /**
     * @param int $totalNumEntries
     * @param string $PageType
     * @param \Google\AdsApi\AdWords\v201802\cm\Feed[] $entries
     */
    public function __construct($totalNumEntries = null, $PageType = null, array $entries = null)
    {
        parent::__construct($totalNumEntries, $PageType);
        $this->entries = $entries;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201802\cm\Feed[]
     */
    public function getEntries()
    {
        return $this->entries;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201802\cm\Feed[] $entries
     * @return \Google\AdsApi\AdWords\v201802\cm\FeedPage
     */
    public function setEntries(array $entries)
    {
        $this->entries = $entries;
        return $this;
    }
}