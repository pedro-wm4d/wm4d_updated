<?php

namespace Google\AdsApi\AdWords\v201802\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class FeedItemTargetService extends \Google\AdsApi\Common\AdsSoapClient
{
    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array('AdSchedule' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\AdSchedule', 'ApiError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ApiError', 'ApiException' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ApiException', 'ApplicationException' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ApplicationException', 'AuthenticationError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\AuthenticationError', 'AuthorizationError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\AuthorizationError', 'ClientTermsError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ClientTermsError', 'Criterion' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Criterion', 'CriterionError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\CriterionError', 'DatabaseError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\DatabaseError', 'DateRange' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\DateRange', 'DistinctError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\DistinctError', 'EntityNotFound' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\EntityNotFound', 'FeedItemAdGroupTarget' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\FeedItemAdGroupTarget', 'FeedItemCampaignTarget' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\FeedItemCampaignTarget', 'FeedItemCriterionTarget' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\FeedItemCriterionTarget', 'FeedItemTarget' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\FeedItemTarget', 'FeedItemTargetError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\FeedItemTargetError', 'FeedItemTargetOperation' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\FeedItemTargetOperation', 'FeedItemTargetPage' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\FeedItemTargetPage', 'FeedItemTargetReturnValue' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\FeedItemTargetReturnValue', 'FieldPathElement' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\FieldPathElement', 'IdError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\IdError', 'InternalApiError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\InternalApiError', 'Keyword' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Keyword', 'ListReturnValue' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ListReturnValue', 'Location' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Location', 'MobileAppCategory' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\MobileAppCategory', 'MobileApplication' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\MobileApplication', 'NegativeFeedItemCriterionTarget' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\NegativeFeedItemCriterionTarget', 'NotEmptyError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\NotEmptyError', 'Operation' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Operation', 'OperationAccessDenied' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\OperationAccessDenied', 'OperatorError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\OperatorError', 'OrderBy' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\OrderBy', 'Page' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Page', 'Paging' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Paging', 'Placement' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Placement', 'Platform' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Platform', 'Predicate' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Predicate', 'QuotaCheckError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\QuotaCheckError', 'RangeError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\RangeError', 'RateExceededError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\RateExceededError', 'ReadOnlyError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ReadOnlyError', 'RejectedError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\RejectedError', 'RequestError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\RequestError', 'RequiredError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\RequiredError', 'Selector' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Selector', 'SelectorError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\SelectorError', 'SizeLimitError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\SizeLimitError', 'SoapHeader' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\SoapHeader', 'SoapResponseHeader' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\SoapResponseHeader', 'StringFormatError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\StringFormatError', 'StringLengthError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\StringLengthError', 'CriterionUserInterest' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\CriterionUserInterest', 'CriterionUserList' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\CriterionUserList', 'Vertical' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Vertical', 'getResponse' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\getResponse', 'mutateResponse' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\mutateResponse', 'queryResponse' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\queryResponse');
    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     */
    public function __construct(array $options = array(), $wsdl = 'https://adwords.google.com/api/adwords/cm/v201802/FeedItemTargetService?wsdl')
    {
        foreach (self::$classmap as $key => $value) {
            if (!isset($options['classmap'][$key])) {
                $options['classmap'][$key] = $value;
            }
        }
        $options = array_merge(array('features' => 1), $options);
        parent::__construct($wsdl, $options);
    }
    /**
     * Returns a list of FeedItemTargets that meet the selector criteria.
     *
     * returned.
     *
     * @param \Google\AdsApi\AdWords\v201802\cm\Selector $selector
     * @return \Google\AdsApi\AdWords\v201802\cm\FeedItemTargetPage
     * @throws \Google\AdsApi\AdWords\v201802\cm\ApiException
     */
    public function get(\Google\AdsApi\AdWords\v201802\cm\Selector $selector)
    {
        return $this->__soapCall('get', array(array('selector' => $selector)))->getRval();
    }
    /**
     * Add and remove FeedItemTargets.
     *
     * @param \Google\AdsApi\AdWords\v201802\cm\FeedItemTargetOperation[] $operations
     * @return \Google\AdsApi\AdWords\v201802\cm\FeedItemTargetReturnValue
     * @throws \Google\AdsApi\AdWords\v201802\cm\ApiException
     */
    public function mutate(array $operations)
    {
        return $this->__soapCall('mutate', array(array('operations' => $operations)))->getRval();
    }
    /**
     * Returns the list of FeedItemTargets that match the query.
     *
     * @param string $query
     * @return \Google\AdsApi\AdWords\v201802\cm\FeedItemTargetPage
     * @throws \Google\AdsApi\AdWords\v201802\cm\ApiException
     */
    public function query($query)
    {
        return $this->__soapCall('query', array(array('query' => $query)))->getRval();
    }
}