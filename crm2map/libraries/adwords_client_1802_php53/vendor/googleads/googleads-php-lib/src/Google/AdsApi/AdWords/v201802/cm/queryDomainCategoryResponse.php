<?php

namespace Google\AdsApi\AdWords\v201802\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class queryDomainCategoryResponse
{
    /**
     * @var \Google\AdsApi\AdWords\v201802\cm\DomainCategoryPage $rval
     */
    protected $rval = null;
    /**
     * @param \Google\AdsApi\AdWords\v201802\cm\DomainCategoryPage $rval
     */
    public function __construct($rval = null)
    {
        $this->rval = $rval;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201802\cm\DomainCategoryPage
     */
    public function getRval()
    {
        return $this->rval;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201802\cm\DomainCategoryPage $rval
     * @return \Google\AdsApi\AdWords\v201802\cm\queryDomainCategoryResponse
     */
    public function setRval($rval)
    {
        $this->rval = $rval;
        return $this;
    }
}