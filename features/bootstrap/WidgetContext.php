<?php

namespace Paymentwall;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

class WidgetContext implements Context
{
    private string $productName = 'Test Default Product Name';
    private int $widgetSignatureVersion;
    private string $widgetCode = 'p10';
    private string $languageCode = '';
    private Widget $widget;
    private string $widgetHtmlContent;

    private FeatureContext $featureContext;

    #[\Behat\Hook\BeforeScenario]
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->featureContext = $environment->getContext(FeatureContext::class);
    }

    protected function getProduct(): array
    {
        switch ($this->featureContext->apiType) {
            case (Config::API_GOODS):
                /**
                 * @todo implement subscriptions, trial, no product
                 */
                return [
                    new Product(
                        'product301',
                        9.99,
                        'USD',
                        $this->productName,
                        Product::TYPE_FIXED
                    ),
                ];

            case (Config::API_VC):
                return [];

            case (Config::API_CART):
                /**
                 * @todo implement custom IDs and prices
                 */
                return [];
        }
    }

    /**
     * @Given /^Widget signature version "([^"]*)"$/
     */
    public function widgetSignatureVersion(int $signatureVersion): void
    {
        $this->widgetSignatureVersion = $signatureVersion;
    }

    /**
     * @Given /^Widget code "([^"]*)"$/
     */
    public function widgetCode(string $widgetCode): void
    {
        $this->widgetCode = $widgetCode;
    }

    /**
     * @Given /^Language code "([^"]*)"$/
     */
    public function languageCode(string $languageCode): void
    {
        $this->languageCode = $languageCode;
    }

    /**
     * @Given /^Product name "([^"]*)"$/
     */
    public function productName(string $productName): void
    {
        $this->productName = $productName;
    }

    /**
     * @When /^Widget is constructed$/
     */
    public function widgetIsConstructed(): void
    {
        $this->widget = new Widget(
            'test_user',
            $this->widgetCode,
            $this->getProduct(),
            [
                'email' => 'user@hostname.com',
                'sign_version' => $this->widgetSignatureVersion,
                'lang' => $this->languageCode,
            ]
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
        if (str_contains($this->widgetHtmlContent, $phrase)) {
            throw new \Exception(
                'Widget HTML content contains "' . $phrase . '"'
            );
        }
    }

    /**
     * @Then /^Widget HTML content should contain "([^"]*)"$/
     */
    public function widgetHtmlContentShouldContain($phrase)
    {
        if (!str_contains($this->widgetHtmlContent, $phrase)) {
            throw new \Exception(
                'Widget HTML content doesn\'t contain "' . $phrase . '" (URL: ' . $this->widget->getUrl() . ')'
            );
        }
    }

    /**
     * @Then /^Widget URL should not contain "([^"]*)"$/
     */
    public function widgetUrlShouldNotContain($phrase)
    {
        if (str_contains($this->widget->getUrl(), $phrase)) {
            throw new \Exception(
                'Widget URL contains "' . $phrase . '"'
            );
        }
    }

    /**
     * @Then /^Widget URL should contain "([^"]*)"$/
     */
    public function widgetUrlShouldContain($phrase)
    {
        if (!str_contains($this->widget->getUrl(), $phrase)) {
            throw new \Exception(
                'Widget URL doesn\'t contain "' . $phrase . '" (URL: ' . $this->widget->getUrl() . ')'
            );
        }
    }
}
