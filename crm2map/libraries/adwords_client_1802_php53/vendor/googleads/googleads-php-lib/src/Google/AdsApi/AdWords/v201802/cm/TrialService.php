<?php

namespace Google\AdsApi\AdWords\v201802\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class TrialService extends \Google\AdsApi\Common\AdsSoapClient
{
    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array('ApiError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ApiError', 'ApiException' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ApiException', 'ApplicationException' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ApplicationException', 'AuthenticationError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\AuthenticationError', 'AuthorizationError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\AuthorizationError', 'BiddingErrors' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\BiddingErrors', 'BudgetError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\BudgetError', 'CampaignError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\CampaignError', 'ClientTermsError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ClientTermsError', 'DatabaseError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\DatabaseError', 'DateError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\DateError', 'DateRange' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\DateRange', 'DateRangeError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\DateRangeError', 'DistinctError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\DistinctError', 'EntityCountLimitExceeded' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\EntityCountLimitExceeded', 'EntityNotFound' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\EntityNotFound', 'FieldPathElement' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\FieldPathElement', 'IdError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\IdError', 'InternalApiError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\InternalApiError', 'ListReturnValue' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ListReturnValue', 'NotEmptyError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\NotEmptyError', 'NullError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\NullError', 'Operation' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Operation', 'OperationAccessDenied' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\OperationAccessDenied', 'OperatorError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\OperatorError', 'OrderBy' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\OrderBy', 'Page' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Page', 'Paging' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Paging', 'Predicate' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Predicate', 'QuotaCheckError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\QuotaCheckError', 'RangeError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\RangeError', 'RateExceededError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\RateExceededError', 'ReadOnlyError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ReadOnlyError', 'RejectedError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\RejectedError', 'RequestError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\RequestError', 'RequiredError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\RequiredError', 'Selector' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Selector', 'SelectorError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\SelectorError', 'SizeLimitError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\SizeLimitError', 'SoapHeader' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\SoapHeader', 'SoapResponseHeader' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\SoapResponseHeader', 'StringFormatError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\StringFormatError', 'StringLengthError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\StringLengthError', 'Trial' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Trial', 'TrialError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\TrialError', 'TrialOperation' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\TrialOperation', 'TrialPage' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\TrialPage', 'TrialReturnValue' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\TrialReturnValue', 'getResponse' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\getResponse', 'mutateResponse' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\mutateResponse', 'queryResponse' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\queryResponse');
    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     */
    public function __construct(array $options = array(), $wsdl = 'https://adwords.google.com/api/adwords/cm/v201802/TrialService?wsdl')
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
     * Loads a TrialPage containing a list of {@link Trial} objects matching the selector.
     *
     * which fields to include
     *
     * retrieving the results.
     *
     * @param \Google\AdsApi\AdWords\v201802\cm\Selector $selector
     * @return \Google\AdsApi\AdWords\v201802\cm\TrialPage
     * @throws \Google\AdsApi\AdWords\v201802\cm\ApiException
     */
    public function get(\Google\AdsApi\AdWords\v201802\cm\Selector $selector)
    {
        return $this->__soapCall('get', array(array('selector' => $selector)))->getRval();
    }
    /**
     * Creates new trials, updates properties and controls the life cycle of existing trials.
     * See {@link TrialService} for details on the trial life cycle.
     *
     * processing the request.
     *
     * @param \Google\AdsApi\AdWords\v201802\cm\TrialOperation[] $operations
     * @return \Google\AdsApi\AdWords\v201802\cm\TrialReturnValue
     * @throws \Google\AdsApi\AdWords\v201802\cm\ApiException
     */
    public function mutate(array $operations)
    {
        return $this->__soapCall('mutate', array(array('operations' => $operations)))->getRval();
    }
    /**
     * Loads a TrialPage containing a list of {@link Trial} objects matching the query.
     *
     * which fields to include
     *
     * retrieving the results.
     *
     * @param string $query
     * @return \Google\AdsApi\AdWords\v201802\cm\TrialPage
     * @throws \Google\AdsApi\AdWords\v201802\cm\ApiException
     */
    public function query($query)
    {
        return $this->__soapCall('query', array(array('query' => $query)))->getRval();
    }
}