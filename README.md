#About Paymentwall
[Paymentwall](http://paymentwall.com/?source=gh) is the leading digital payments platform for globally monetizing digital goods and services. Paymentwall assists game publishers, dating sites, rewards sites, SaaS companies and many other verticals to monetize their digital content and services. 
Merchants can plugin Paymentwall's API to accept payments from over 100 different methods including credit cards, debit cards, bank transfers, SMS/Mobile payments, prepaid cards, eWallets, landline payments and others. 

To sign up for a Paymentwall Merchant Account, [click here](http://paymentwall.com/signup/merchant?source=gh).

#Paymentwall PHP Library
This library allows developers to use [Paymentwall APIs](http://paymentwall.com/en/documentation/API-Documentation/722?source=gh) (Virtual Currency, Digital Goods featuring recurring billing, and Virtual Cart).

To use Paymentwall, all you need to do is to sign up for a Paymentwall Merchant Account so you can setup an Application designed for your site.
To open your merchant account and set up an application, you can [sign up here](http://paymentwall.com/signup/merchant?source=gh).


#Code Sample
##Initializing Paymentwall
<pre><code>require_once('/path/to/paymentwall-php/libs/paymentwall.php');
Paymentwall_Base::setApiType(Paymentwall_Base::API_GOODS); // API_VC for Virtual Currency, API_CART for Cart
Paymentwall_Base::setAppKey('YOUR_APPLICATION_KEY'); // available inside of your merchant account
Paymentwall_Base::setSecretKey('YOUR_SECRET_KEY'); // available inside of your merchant account
</code></pre>

##Widget Call
[Web API details](http://www.paymentwall.com/en/documentation/Digital-Goods-API/710#paymentwall_widget_call_flexible_widget_call)

The widget is a payment page hosted by Paymentwall that embeds the entire payment flow: selecting the payment method, completing the billing details, and providing customer support via the Help section. You can redirect the users to this page or embed it via iframe. Below is an example that renders an iframe with Paymentwall Widget.

###Virtual Currency
<pre><code>$widget = new Paymentwall_Widget(
	'yeexel', // id of the end-user who's making the payment
	'p10_1', // widget code, e.g. p1; can be picked inside of your merchant account
	array(), // array of products - leave blank for Virtual Currency API
	array('sign_version' => 1) // additional parameters
);
echo $widget->getHtmlCode();
// Now you can embed the iframe with Paymentwall widget into your website
// &lt;iframe src=&quot;https://api.paymentwall.com/api/ps?key=0b1552192f37f9dd84150a39be14a5e9&amp;uid=yeexel&amp;widget=p10_1&amp;sign_version=1&amp;sign=7c0be7b97bc93de6074eed243c65aa77&quot;  frameborder=&quot;0&quot; width=&quot;750&quot; height=&quot;800&quot;&gt;&lt;/iframe&gt;</code></pre>

###Digital Goods
<pre><code>$widget = new Paymentwall_Widget(
  'user40012',									// id of the end-user who's making the payment
  'p1_1',										// widget code, e.g. p1; can be picked inside of your merchant account
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
  ),
  array('email' => 'user@hostname.com')			// additional parameters
);
echo $widget->getHtmlCode();
</pre></code>

<h3>Cart</h3>
<pre><code>$widget = new Paymentwall_Widget(
	'yeexel', // id of the end-user who's making the payment
	'p1_1', // widget code, e.g. p1; can be picked inside of your merchant account,
	array(
		new Paymentwall_Product('1', 3.33, 'EUR'), // first product in cart
		new Paymentwall_Product('2', 7.77, 'EUR')  // second product in cart
	),
	array('evaluation' => '1') // additional params
);
echo $widget->getUrl();</code></pre>

##Pingback Processing
[Web API details](http://www.paymentwall.com/en/documentation/Digital-Goods-API/710#paymentwall_widget_call_pingback_processing)

The Pingback is a webhook notifying about a payment being made. Pingbacks are sent via HTTP/HTTPS to your servers. To process pingbacks use the following code:
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
