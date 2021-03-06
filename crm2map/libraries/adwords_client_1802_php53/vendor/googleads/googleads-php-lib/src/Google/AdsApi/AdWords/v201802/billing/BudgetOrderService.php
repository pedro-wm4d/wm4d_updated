<?php

namespace Google\AdsApi\AdWords\v201802\billing;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class BudgetOrderService extends \Google\AdsApi\Common\AdsSoapClient
{
    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array('ApiError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ApiError', 'ApiException' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ApiException', 'ApplicationException' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ApplicationException', 'AuthenticationError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\AuthenticationError', 'AuthorizationError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\AuthorizationError', 'ClientTermsError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ClientTermsError', 'ComparableValue' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ComparableValue', 'DatabaseError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\DatabaseError', 'DateError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\DateError', 'DateRange' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\DateRange', 'DistinctError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\DistinctError', 'DoubleValue' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\DoubleValue', 'EntityNotFound' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\EntityNotFound', 'FieldPathElement' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\FieldPathElement', 'IdError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\IdError', 'InternalApiError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\InternalApiError', 'ListReturnValue' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ListReturnValue', 'LongValue' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\LongValue', 'Money' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Money', 'NewEntityCreationError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\NewEntityCreationError', 'NotEmptyError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\NotEmptyError', 'NotWhitelistedError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\NotWhitelistedError', 'NullError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\NullError', 'NumberValue' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\NumberValue', 'Operation' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Operation', 'OperationAccessDenied' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\OperationAccessDenied', 'OperatorError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\OperatorError', 'OrderBy' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\OrderBy', 'Page' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Page', 'Paging' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Paging', 'PagingError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\PagingError', 'Predicate' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Predicate', 'QuotaCheckError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\QuotaCheckError', 'RangeError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\RangeError', 'RateExceededError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\RateExceededError', 'ReadOnlyError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\ReadOnlyError', 'RejectedError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\RejectedError', 'RequestError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\RequestError', 'RequiredError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\RequiredError', 'Selector' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\Selector', 'SelectorError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\SelectorError', 'SizeLimitError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\SizeLimitError', 'SoapHeader' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\SoapHeader', 'SoapResponseHeader' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\SoapResponseHeader', 'StatsQueryError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\StatsQueryError', 'StringFormatError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\StringFormatError', 'StringLengthError' => 'Google\\AdsApi\\AdWords\\v201802\\cm\\StringLengthError', 'BillingAccount' => 'Google\\AdsApi\\AdWords\\v201802\\billing\\BillingAccount', 'BudgetOrder' => 'Google\\AdsApi\\AdWords\\v201802\\billing\\BudgetOrder', 'BudgetOrderError' => 'Google\\AdsApi\\AdWords\\v201802\\billing\\BudgetOrderError', 'BudgetOrderOperation' => 'Google\\AdsApi\\AdWords\\v201802\\billing\\BudgetOrderOperation', 'BudgetOrderPage' => 'Google\\AdsApi\\AdWords\\v201802\\billing\\BudgetOrderPage', 'BudgetOrderRequest' => 'Google\\AdsApi\\AdWords\\v201802\\billing\\BudgetOrderRequest', 'BudgetOrderReturnValue' => 'Google\\AdsApi\\AdWords\\v201802\\billing\\BudgetOrderReturnValue', 'CustomerOrderLineError' => 'Google\\AdsApi\\AdWords\\v201802\\billing\\CustomerOrderLineError', 'getResponse' => 'Google\\AdsApi\\AdWords\\v201802\\billing\\getResponse', 'getBillingAccountsResponse' => 'Google\\AdsApi\\AdWords\\v201802\\billing\\getBillingAccountsResponse', 'mutateResponse' => 'Google\\AdsApi\\AdWords\\v201802\\billing\\mutateResponse');
    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     */
    public function __construct(array $options = array(), $wsdl = 'https://adwords.google.com/api/adwords/billing/v201802/BudgetOrderService?wsdl')
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
     * Gets a list of {@link BudgetOrder}s using the generic selector.
     *
     * fields are returned. Stats are not yet supported.
     *
     * @param \Google\AdsApi\AdWords\v201802\cm\Selector $serviceSelector
     * @return \Google\AdsApi\AdWords\v201802\billing\BudgetOrderPage
     * @throws \Google\AdsApi\AdWords\v201802\cm\ApiException
     */
    public function get(\Google\AdsApi\AdWords\v201802\cm\Selector $serviceSelector)
    {
        return $this->__soapCall('get', array(array('serviceSelector' => $serviceSelector)))->getRval();
    }
    /**
     * Returns all the open/active BillingAccounts associated with the current manager.
     *
     * @return \Google\AdsApi\AdWords\v201802\billing\BillingAccount[]
     * @throws \Google\AdsApi\AdWords\v201802\cm\ApiException
     */
    public function getBillingAccounts()
    {
        return $this->__soapCall('getBillingAccounts', array(array()))->getRval();
    }
    /**
     * Adds, updates, or removes budget orders. Supported operations are:
     * <p><code>ADD</code>: Adds a {@link BudgetOrder} to the billing account
     * specified by the billing account ID.</p>
     * <p><code>SET</code>: Sets the start/end date and amount of the
     * {@link BudgetOrder}.</p>
     * <p><code>REMOVE</code>: Cancels the {@link BudgetOrder} (status change).</p>
     * <p class="warning"><b>Warning:</b> The <code>BudgetOrderService</code>
     * is limited to one operation per mutate request. Any attempt to make more
     * than one operation will result in an <code>ApiException</code>.</p>
     * <p class="note"><b>Note:</b> This action is available only on a whitelist basis.</p>
     * support one operation per mutate call</b>.
     *
     * @param \Google\AdsApi\AdWords\v201802\billing\BudgetOrderOperation[] $operations
     * @return \Google\AdsApi\AdWords\v201802\billing\BudgetOrderReturnValue
     * @throws \Google\AdsApi\AdWords\v201802\cm\ApiException
     */
    public function mutate(array $operations)
    {
        return $this->__soapCall('mutate', array(array('operations' => $operations)))->getRval();
    }
}