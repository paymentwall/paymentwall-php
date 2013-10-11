#About Paymentwall
[Paymentwall](http://paymentwall.com/?source=gh) is the leading digital payments platform for globally monetizing digital goods and services. Paymentwall assists game publishers, dating sites, rewards sites, SaaS companies and many other verticals to monetize their digital content and services. 
Merchants can accept payments via Paymentwall using many various payment methods, such as credit cards, debit cards, bank transfers, sms payments, prepaid cards, eWallets, landline payments.
To sign up for a Paymentwall Merchant Account, [click here](http://paymentwall.com/signup/merchant?source=gh).

#Paymentwall PHP SDK
This SDK allows developers to use [Paymentwall APIs](http://paymentwall.com/en/documentation/API-Documentation/722?source=gh) (Virtual Currency, Digital Goods with recurring billing, Cart).

To use Paymentwall, all you would need to have is a Paymentwall Merchant Account and a Application set up under your account.
To create a merchant account and set up an application, you can [sign up here within a minute](http://paymentwall.com/signup/merchant?source=gh).


#Code Sample

Below is a code sample for Digital Goods API with Flexible Widget Call

##Initializing Paymentwall
<pre><code>require_once('/path/to/paymentwall-php/libs/paymentwall.php');
Paymentwall_Base::setApiType(Paymentwall_Base::API_GOODS);
Paymentwall_Base::setAppKey('YOUR_APPLICATION_KEY'); // available inside of your merchant account
Paymentwall_Base::setSecretKey('YOUR_SECRET_KEY'); // available inside of your merchant account
</code></pre>

##Widget Call
[Web API details](http://www.paymentwall.com/en/documentation/Digital-Goods-API/710#paymentwall_widget_call_flexible_widget_call)

Widget is a payment page hosted by Paymentwall that embeds the entire payment flow: selecting the payment method, providing the billing data, investigating the payment status via Help section. You can redirect the users to this page or embed it via iframe. Below is an example that renders an iframe with Paymentwall Widget.

<pre><code>$widget = new Paymentwall_Widget();
echo $widget->getCode(
  'user40012',
  'p1_1',
  array('email' => 'user@hostname.com'),
  array(
    new Paymentwall_Product(
      'product301',                             // id of the product in your system
      9.99,                                     // price
      'USD',                                    // currency code
      'Gold Membership',                        // product name
      Paymentwall_Product::TYPE_SUBSCRIPTION,   // this is a time-based product
      1,                                        // duration is 1
      Paymentwall_Product::PERIOD_TYPE_MONTH,   //               month
      true                                      // recurring
    )
  )
);</pre></code>

##Pingback Processing
[Web API details](http://www.paymentwall.com/en/documentation/Digital-Goods-API/710#paymentwall_widget_call_pingback_processing)

Pingback is a webhook notifying about a payment being made. Pingbacks are sent via HTTP/HTTPS to your servers. To process pingbacks use the following code:
<pre><code>$pingback = new Paymentwall_Pingback($\_GET, $\_SERVER['REMOTE_ADDR']);
if ($pingback->validate()) {
  $productId = $pingback->getProduct()->getId();
  if ($pingback->isDeliverable()) {
	// deliver the product
  } else if ($pingback->isCancelable()) {
	// withdraw the product
  } 
  echo 'OK'; // Paymentwall expects response to be OK, otherwise the pingback will be resent
} else {
  echo $pingback->getErrorSummary();
}</pre></code>
