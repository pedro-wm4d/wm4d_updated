<?php

namespace Google\AdsApi\AdWords\v201802\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class SharedCriterionService extends \Google\AdsApi\Common\AdsSoapClient
{
    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array('ApiError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ApiError', 'ApiException' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ApiException', 'ApplicationException' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ApplicationException', 'AuthenticationError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\AuthenticationError', 'AuthorizationError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\AuthorizationError', 'ClientTermsError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ClientTermsError', 'Criterion' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Criterion', 'CriterionError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\CriterionError', 'DatabaseError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\DatabaseError', 'DateRange' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\DateRange', 'DistinctError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\DistinctError', 'EntityCountLimitExceeded' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\EntityCountLimitExceeded', 'EntityNotFound' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\EntityNotFound', 'FieldPathElement' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\FieldPathElement', 'IdError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\IdError', 'InternalApiError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\InternalApiError', 'Keyword' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Keyword', 'ListReturnValue' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ListReturnValue', 'MobileAppCategory' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\MobileAppCategory', 'MobileApplication' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\MobileApplication', 'NotEmptyError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\NotEmptyError', 'NullError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\NullError', 'Operation' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Operation', 'OperationAccessDenied' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\OperationAccessDenied', 'OperatorError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\OperatorError', 'OrderBy' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\OrderBy', 'Page' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Page', 'Paging' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Paging', 'Placement' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Placement', 'Predicate' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Predicate', 'QueryError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\QueryError', 'QuotaCheckError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\QuotaCheckError', 'RangeError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\RangeError', 'RateExceededError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\RateExceededError', 'ReadOnlyError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ReadOnlyError', 'RejectedError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\RejectedError', 'RequestError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\RequestError', 'RequiredError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\RequiredError', 'Selector' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Selector', 'SelectorError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\SelectorError', 'SharedCriterion' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\SharedCriterion', 'SharedCriterionError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\SharedCriterionError', 'SharedCriterionOperation' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\SharedCriterionOperation', 'SharedCriterionPage' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\SharedCriterionPage', 'SharedCriterionReturnValue' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\SharedCriterionReturnValue', 'SizeLimitError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\SizeLimitError', 'SoapHeader' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\SoapHeader', 'SoapResponseHeader' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\SoapResponseHeader', 'StringFormatError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\StringFormatError', 'StringLengthError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\StringLengthError', 'CriterionUserInterest' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\CriterionUserInterest', 'CriterionUserList' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\CriterionUserList', 'Vertical' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Vertical', 'YouTubeChannel' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\YouTubeChannel', 'YouTubeVideo' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\YouTubeVideo', 'getResponse' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\getResponse', 'mutateResponse' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\mutateResponse', 'queryResponse' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\queryResponse');
    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     */
    public function __construct(array $options = array(), $wsdl = 'https://adwords.google.com/api/adwords/cm/v201802/SharedCriterionService?wsdl')
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
     * Returns a list of SharedCriterion that meets the selector criteria.
     *
     * @param \Google\AdsApi\AdWords\v201802\cm\Selector $selector
     * @return \Google\AdsApi\AdWords\v201802\cm\SharedCriterionPage
     * @throws \Google\AdsApi\AdWords\v201802\cm\ApiException
     */
    public function get(\Google\AdsApi\AdWords\v201802\cm\Selector $selector)
    {
        return $this->__soapCall('get', array(array('selector' => $selector)))->getRval();
    }
    /**
     * Adds, removes criteria in a shared set.
     *
     * {@code operations} array.
     *
     * @param \Google\AdsApi\AdWords\v201802\cm\SharedCriterionOperation[] $operations
     * @return \Google\AdsApi\AdWords\v201802\cm\SharedCriterionReturnValue
     * @throws \Google\AdsApi\AdWords\v201802\cm\ApiException
     */
    public function mutate(array $operations)
    {
        return $this->__soapCall('mutate', array(array('operations' => $operations)))->getRval();
    }
    /**
     * Returns the list of SharedCriterion that match the query.
     *
     * @param string $query
     * @return \Google\AdsApi\AdWords\v201802\cm\SharedCriterionPage
     * @throws \Google\AdsApi\AdWords\v201802\cm\ApiException
     */
    public function query($query)
    {
        return $this->__soapCall('query', array(array('query' => $query)))->getRval();
    }
}