<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

class WidgetContext implements Context
{
    private string $productName = 'Test Default Product Name';
    private int $widgetSignatureVersion;
    private string $widgetCode = 'p10';
    private string $languageCode = '';
    private \Paymentwall\Widget $widget;
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
            case (\Paymentwall\Config::API_GOODS):
                /**
                 * @todo implement subscriptions, trial, no product
                 */
                return [
                    new Paymentwall\Product(
                        'product301',
                        9.99,
                        'USD',
                        $this->productName,
                        Paymentwall\Product::TYPE_FIXED
                    ),
                ];

            case (\Paymentwall\Config::API_VC):
                return [];

            case (\Paymentwall\Config::API_CART):
                /**
                 * @todo implement custom IDs and prices
                 */
                return [];
        }
    }

    #[\Behat\Step\Given('Widget signature version ":signatureVersion"')]
    public function widgetSignatureVersion(int $signatureVersion): void
    {
        $this->widgetSignatureVersion = $signatureVersion;
    }

    #[\Behat\Step\Given('Widget code ":widgetCode"')]
    public function widgetCode(string $widgetCode): void
    {
        $this->widgetCode = $widgetCode;
    }

    #[\Behat\Step\Given('Language code ":languageCode"')]
    public function languageCode(string $languageCode): void
    {
        $this->languageCode = $languageCode;
    }

    #[\Behat\Step\Given('Product name ":productName"')]
    public function productName(string $productName): void
    {
        $this->productName = $productName;
    }

    #[\Behat\Step\When('Widget is constructed')]
    public function widgetIsConstructed(): void
    {
        $this->widget = new \Paymentwall\Widget(
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

    #[\Behat\Step\When('Widget HTML content is loaded')]
    public function widgetHtmlContentIsLoaded(): void
    {
        $this->widgetHtmlContent = file_get_contents($this->widget->getUrl());
    }

    #[\Behat\Step\Then('Widget HTML content should not contain ":phrase"')]
    public function widgetHtmlContentShouldNotContain($phrase): void
    {
        if (str_contains($this->widgetHtmlContent, $phrase)) {
            throw new \Exception(
                'Widget HTML content contains "' . $phrase . '"'
            );
        }
    }

    #[\Behat\Step\Then('Widget HTML content should contain ":phrase"')]
    public function widgetHtmlContentShouldContain($phrase): void
    {
        if (!str_contains($this->widgetHtmlContent, $phrase)) {
            throw new \Exception(
                'Widget HTML content doesn\'t contain "' . $phrase . '" (URL: ' . $this->widget->getUrl() . ')'
            );
        }
    }

    #[\Behat\Step\Then('Widget URL should not contain ":phrase"')]
    public function widgetUrlShouldNotContain($phrase): void
    {
        if (str_contains($this->widget->getUrl(), $phrase)) {
            throw new \Exception(
                'Widget URL contains "' . $phrase . '"'
            );
        }
    }

    #[\Behat\Step\Then('Widget URL should contain ":phrase"')]
    public function widgetUrlShouldContain($phrase): void
    {
        if (!str_contains($this->widget->getUrl(), $phrase)) {
            throw new \Exception(
                'Widget URL doesn\'t contain "' . $phrase . '" (URL: ' . $this->widget->getUrl() . ')'
            );
        }
    }
}
