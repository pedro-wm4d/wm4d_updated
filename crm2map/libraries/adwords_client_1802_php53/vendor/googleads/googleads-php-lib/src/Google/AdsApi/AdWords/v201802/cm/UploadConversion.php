<?php

namespace Google\AdsApi\AdWords\v201802\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class UploadConversion extends \Google\AdsApi\AdWords\v201802\cm\ConversionTracker
{
    /**
     * @var boolean $isExternallyAttributed
     */
    protected $isExternallyAttributed = null;
    /**
     * @param int $id
     * @param int $originalConversionTypeId
     * @param string $name
     * @param string $status
     * @param string $category
     * @param string $googleEventSnippet
     * @param string $googleGlobalSiteTag
     * @param string $dataDrivenModelStatus
     * @param int $conversionTypeOwnerCustomerId
     * @param int $viewthroughLookbackWindow
     * @param int $ctcLookbackWindow
     * @param string $countingType
     * @param float $defaultRevenueValue
     * @param string $defaultRevenueCurrencyCode
     * @param boolean $alwaysUseDefaultRevenueValue
     * @param boolean $excludeFromBidding
     * @param string $attributionModelType
     * @param string $mostRecentConversionDate
     * @param string $lastReceivedRequestTime
     * @param string $ConversionTrackerType
     * @param boolean $isExternallyAttributed
     */
    public function __construct($id = null, $originalConversionTypeId = null, $name = null, $status = null, $category = null, $googleEventSnippet = null, $googleGlobalSiteTag = null, $dataDrivenModelStatus = null, $conversionTypeOwnerCustomerId = null, $viewthroughLookbackWindow = null, $ctcLookbackWindow = null, $countingType = null, $defaultRevenueValue = null, $defaultRevenueCurrencyCode = null, $alwaysUseDefaultRevenueValue = null, $excludeFromBidding = null, $attributionModelType = null, $mostRecentConversionDate = null, $lastReceivedRequestTime = null, $ConversionTrackerType = null, $isExternallyAttributed = null)
    {
        parent::__construct($id, $originalConversionTypeId, $name, $status, $category, $googleEventSnippet, $googleGlobalSiteTag, $dataDrivenModelStatus, $conversionTypeOwnerCustomerId, $viewthroughLookbackWindow, $ctcLookbackWindow, $countingType, $defaultRevenueValue, $defaultRevenueCurrencyCode, $alwaysUseDefaultRevenueValue, $excludeFromBidding, $attributionModelType, $mostRecentConversionDate, $lastReceivedRequestTime, $ConversionTrackerType);
        $this->isExternallyAttributed = $isExternallyAttributed;
    }
    /**
     * @return boolean
     */
    public function getIsExternallyAttributed()
    {
        return $this->isExternallyAttributed;
    }
    /**
     * @param boolean $isExternallyAttributed
     * @return \Google\AdsApi\AdWords\v201802\cm\UploadConversion
     */
    public function setIsExternallyAttributed($isExternallyAttributed)
    {
        $this->isExternallyAttributed = $isExternallyAttributed;
        return $this;
    }
}