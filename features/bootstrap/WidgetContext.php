<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;


class WidgetContext implements Context
{
    private $productName = 'Test Default Product Name';
    private $widgetSignatureVersion = null;
    private $widgetCode = 'p10';
    private $languageCode = null;
    private $widget = null;
    private $widgetHtmlContent = null;

    /** @var FeatureContext */
    private $featureContext;

    #[\Behat\Hook\BeforeScenario]
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->featureContext = $environment->getContext(FeatureContext::class);
    }

    protected function getWidgetSignatureVersion() {
        return $this->widgetSignatureVersion;
    }

    protected function getUserId() {
        return 'test_user';
    }

    protected function getWidgetCode() {
        return $this->widgetCode;
    }

    protected function getLanguageCode() {
        return $this->languageCode;
    }

    protected function getProduct() {
        switch ($this->featureContext->apiType) {
            case (Paymentwall_Base::API_GOODS):
                /**
                 * @todo implement subscriptions, trial, no product
                 */
                return array(
                    new Paymentwall_Product(
                        'product301',                           
                        9.99,                                   
                        'USD',                                  
                        $this->productName,
                        Paymentwall_Product::TYPE_FIXED
                    )
                );

            case (Paymentwall_Base::API_VC):
                return array();

            case (Paymentwall_Base::API_CART):
                /**
                 * @todo implement custom IDs and prices
                 */
                return array();
        }
    }

    /**
     * @Given /^Widget signature version "([^"]*)"$/
     */
    public function widgetSignatureVersion($signatureVersion)
    {
        $this->widgetSignatureVersion = $signatureVersion;
    }

    /**
     * @Given /^Widget code "([^"]*)"$/
     */
    public function widgetCode($widgetCode)
    {
        $this->widgetCode = $widgetCode;
    }

    /**
     * @Given /^Language code "([^"]*)"$/
     */
    public function languageCode($languageCode)
    {
        $this->languageCode = $languageCode;
    }

    /**
     * @Given /^Product name "([^"]*)"$/
     */
    public function productName($productName)
    {
        $this->productName = $productName;
    }

    /**
     * @When /^Widget is constructed$/
     */
    public function widgetIsConstructed()
    {
        $this->widget = new Paymentwall_Widget(
            $this->getUserId(),
            $this->getWidgetCode(),
            $this->getProduct(),
            array(
                'email' => 'user@hostname.com', 
                'sign_version' => $this->getWidgetSignatureVersion(),
                'lang' => $this->getLanguageCode()
            )
        );
    }

    /**
     * @When /^Widget HTML content is loaded$/
     */
    public function widgetHtmlContentIsLoaded()
    {
        $this->widgetHtmlContent = file_get_contents($this->widget->getUrl());
    }

    /**
     * @Then /^Widget HTML content should not contain "([^"]*)"$/
     */
    public function widgetHtmlContentShouldNotContain($phrase)
    {
        if (strpos($this->widgetHtmlContent, $phrase) !== false) {
            throw new Exception(
                'Widget HTML content contains "' . $phrase . '"'
            );
        }
    }

    /**
     * @Then /^Widget HTML content should contain "([^"]*)"$/
     */
    public function widgetHtmlContentShouldContain($phrase)
    {
        if (strpos($this->widgetHtmlContent, $phrase) === false) {
            throw new Exception(
                'Widget HTML content doesn\'t contain "' . $phrase . '" (URL: ' . $this->widget->getUrl() .')'
            );
        }
    }

    /**
     * @Then /^Widget URL should not contain "([^"]*)"$/
     */
    public function widgetUrlShouldNotContain($phrase)
    {
        if (strpos($this->widget->getUrl(), $phrase) !== false) {
            throw new Exception(
                'Widget URL contains "' . $phrase . '"'
            );
        }
    }

    /**
     * @Then /^Widget URL should contain "([^"]*)"$/
     */
    public function widgetUrlShouldContain($phrase)
    {
        if (strpos($this->widget->getUrl(), $phrase) === false) {
            throw new Exception(
                'Widget URL doesn\'t contain "' . $phrase . '" (URL: ' . $this->widget->getUrl() .')'
            );
        }
    }
}
