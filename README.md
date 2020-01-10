# Commerce for Statamic

Commerce is a powerful e-commerce solution for Statamic (v3) sites. It gives developers best of both worlds - A fantasic CMS and a fantastic e-commerce addon. For content editors, this means they get to use the beautiful Statamic Control Panel.

> Please learn how to use Statamic first before using Commerce.

## Database vs flat-file

Unlike Statamic itself using flat-files, **Commerce uses a MySQL database**. There are a variety of advantages a *real* database gives us. Things like relationships, security, speed. We initially tried to make Commerce work just on files but it ended up being near enough impossible, especially if we want it to scale to more than just a few products.

## Installing

### Server Requirements

Commerce requires that you have Statamic 3 already installed [(with all of its requirements)](https://statamic.dev/requirements) and that you setup a [MySQL database](https://laravel.com/docs/5.8/database) (this is where Commerce products, orders and customers will live).

### Installation

During development, here's how you install Commerce for Statamic:

1. Clone this repository to `./addons/damcclean/commerce` - `git clone git@github.com:damcclean/commerce.git addons/damcclean/commerce`
2. Run `composer install` inside the `./addons/damcclean/commerce` folder.
3. In your site's main `composer.json` file, add the following few lines:

```json
  "require": {
      ...
      "damcclean/commerce": "dev-master"
  }
  
  ...

  "repositories": [
        {
            "type": "path",
            "url": "addons/damcclean/commerce"
        }
    ]
```

4. Run `composer install && composer update`

5. Run `php artisan vendor:publish` and select the option `Damcclean\Commerce\CommerceServiceProvider`.

6. You'll also need to run our database migrations and seeders to get your database setup. `php artisan migrate && php artisan db:seed`

7. Another thing you'll want to do before things start working is to add some URLs into your app's `VerifyCsrfToken.php` file. There's a statamic bug meaning we can't use csrf tokens in some places. We'll hopefully have this fixed before we launch.

```php
<?php

    ...

    protected $except = [
            '/cart', '/cart/*', '/checkout', '/products/search',
    ];
```

8. Last but not least, you'll want to [setup Stripe](./stripe.md#setting-stripe-up) or you won't be able to accept payments.

## Configuration

All of the configuration options for Commerce live in the `config/commerce.php` file. In there you can set things like the address of your store, stripe settings and routes.

### Company Information

```php
<?php

return [

    /**
     * Company information
     *
     * This will be shown on any receipts sent to customers.
     */

    'company' => [
        'name' => '',
        'address' => '',
        'city' => '',
        'country' => '',
        'zip_code' => '',
        'email' => ''
    ],
```

Here you can fill in your company's name, address and email. This information will be displayed in receipts and any other documentation produced by Commerce.

### Stripe

```php
<?php

return [

    ...

    /**
     * Stripe
     *
     * We need these keys so your customers can purchase
     * products and so you can receive the money.
     *
     * You can find these keys here: https://dashboard.stripe.com/apikeys
     */

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET')
    ],
```

This is where Commerce gets the API credentials for your Stripe account, you'll need to enter this or you can't make any payments. Remember to use your test API keys in development and staging and your live ones in production.

### Routes

```php
<?php

return [

    ...

    /**
     * Routes
     *
     * Commerce provides a set of web routes to make your store
     * function. You can change these routes if you have other
     * preferences.
     */

    'routes' => [

        /**
         * Cart
         *
         * - (add) Adds an item to the customers' cart.
         * - (clear) Clears all items from the customers' cart.
         * - (delete) Removes an item from the customers' cart.
         */

        'cart' => [
            'add' => '/cart',
            'clear' => '/cart/clear',
            'delete' => '/cart/delete',
        ],

        /**
         * Checkout
         *
         * - (show) Displays the checkout view to the user
         * - (store) Processes the users' order
         */

        'checkout' => [
            'show' => '/checkout',
            'store' => '/checkout',
        ],

        /**
         * Products
         *
         * - (index) Displays all products
         * - (search) Displays a product search to the user
         * - (show) Displays a product page
         */

        'products' => [
            'index' => '/products',
            'search' => '/products/search',
            'show' => '/products/{product}',
        ],

        'thanks' => '/thanks', // Page user is redirected to once order has been processed.
        'redeem_coupon' => '/redeem-coupon', // Endpoint where we check if a coupon provided by the customer is valid

    ],

    ...
```

We give the option of changing the structure of the routes that Commerce provides. For example, if you wanted to change the checkout URL from being `/checkout` (the default) to `/pay-here`, you'd just change the value of `checkout.store`.

## Concepts

### Orders

When the checkout process has been completed, an order is created. You can view your orders in the Control Panel.

When order is created, we do a few things:

* Check if the customer is new to the store of if they are a returning customer.
* We'll set the status of the order to `New` (by default).

Each order gets its own order ID which can be found in the Control Panel. These are used so you can identify orders.

### Customers

Every time an order is created, we also create a customer (if one doesn't already exist). 

A customer must always have a name and an email address. Each customer will also have two addresses, one as their billing address and one as their shipping address.

### Products

Products are the things that can be added to the customers' cart and that can be added to orders. However, it's not the actual product that is added to the order, it is a variant of that product. 

For example, you might have one t-shirt product but it might be available in different sizes and colours. So for each of those variations, you would create another variant of the product within the Control Panel.

Each product, will also have a default variant, this is the one that will be displayed as the product price in the front-end, unless you change it to be otherwise.

#### Templating

##### All products

Display all available products in your store.

```html
{{ commerce:products }}
    <a href="{{ url }}">
        <h2>{{ title }}</h2>
        <h4>{{ price }}</h4>
    </a>
{{ /commerce:products }}
```

##### Products in a category

Display all products within a certain product category.

```html
{{ commerce:products category='clothing' }}
    <a href="{{ url }}">
        <h2>{{ title }}</h2>
        <h4>{{ price }}</h4>
    </a>
{{ /commerce:products }}
```

##### Count

You can get the number of products available in your store by using `{{ commerce:products count='true' }}`.

##### Show Disabled

By default, disabled products aren't shown, however if you'd like to show them, use the attribute `show_displayed='true'`.

### Product Categories

Product Categories are like taxonomies. They can be attached to products and they help to organise your store and define product URLs.

#### Templating

##### All categories

Display all product categories.

```html
{{ commerce:categories }}
    <a href="{{ url }}">
        {{ title }}
    </a>
{{ /commerce:categories }}
```

##### Count

You can get the number of product categories available in your store by using `{{ commerce:categories count='true' }}`.

## Stripe (Payment Gateway)

[Stripe](https://stripe.com/) is one of the most popular payment gateways available which is why it's a first class citizen of Commerce for Statamic.

It [doesn't support all countries and currencies](https://stripe.com/global). In which case, it might not be best fit to use Commerce if you need your store to support a country or currency not yet supported by Stripe.

### How the Checkout flow work

If you want to understand how you get from the checkout page to having money being deducted from a customer's bank account, here's how that works (from a technical perspective).

1. We'll create a thing called a 'PaymentIntent'. A Payment Intent is a thing that tells Stripe that we intend on billing a customer a certain amount. We'll generate a 'PaymentIntent' whenever the user visits the Checkout page of your store.
2. When the user enters their card details into the field and they submit the checkout form, we'll go and ask Stripe to go along with actually billing the customer.

### Strong Customer Authentication (SCA)

Strong Customer Authentication is a new piece of legislation introduced by the European Union (and adopted by all member countries) which requires banks to ask for verification that a customer is who they say they are when making a purchase online.

There are three ways a customer could have to verify themselves, two have to be used for verification.

1. Something you know (a password or security questions)
2. Something you have (a mobile phone)
3. Something you are (fingerprint) 

Usually, Strong Customer Authentication is only used for items over a certain threshold meaning low value items may not require authentication.

Originally, SCA was meant to come into force on the 14th of September but it's been held back as many financial institutions and online merchants were not and possibly still not compatible with the new legislation.

### Test Cards

While in Stripe test mode, you can use [test cards](https://stripe.com/docs/testing#cards) to make sure the checkout flow works properly.

## Events

Commerce provides a few events which you can hook into in your `EventServiceProvider`.

### [`AddedToCart`](https://github.com/damcclean/commerce/blob/master/src/Events/AddedToCart.php)

Every time the user adds a product variant to their cart, this event is dispatched with the `product` and `variant` that are added.

### [`CheckoutComplete`](https://github.com/damcclean/commerce/blob/master/src/Events/CheckoutComplete.php)

Once the user has completed the checkout flow and an order has been created within Commerce, this event is dispatched with the `order` and `customer`.

### [`CouponUsed`](https://github.com/damcclean/commerce/blob/master/src/Events/CouponUsed.php)

When a user redeems a valid coupon this event is dispatched with the `coupon`.

### [`NewCustomerCreated`](https://github.com/damcclean/commerce/blob/master/src/Events/NewCustomerCreated.php)

When a user completes an order, we look to see if the customer is new or already exists in the store database. If they are new, we dispatch this event with the `customer`.

### [`OrderStatusUpdated`](https://github.com/damcclean/commerce/blob/master/src/Events/OrderStatusUpdated.php)

When the status of an order is changed from the Control Panel, then this event is dispatched with the `order`.

### [`ProductOutOfStock`](https://github.com/damcclean/commerce/blob/master/src/Events/ProductOutOfStock.php)

When a product variant has run out of stock, this event will be dispatched with the `product`.

### [`ProductStockRunningLow.php`](https://github.com/damcclean/commerce/blob/master/src/Events/ProductStockRunningLow.php)

When a product variant is running low on stock, this event will be dispatched with the `product`.

### [`ReturnCustomer`](https://github.com/damcclean/commerce/blob/master/src/Events/ReturnCustomer.php)

When a user completes an order, we will look to see if the customer is new or already exists in the store database. If they already exist, we will dispatch this event with the `customer`.

## Front-end

By default, Commerce provides you with a boilerplate front-end. We wouldn't recommend using this boilerplate in product but only as an example of how things work together.

The boilerplate should get published to your `resources/views/vendor` directory during installation, but you can also find them [on Github](https://github.com/damcclean/commerce/tree/master/resources/views/web).

### Tags

#### `{{ commerce }}`

#### `{{ commerce:stripeKEY }}`

Outputs the Stripe public key.

#### `{{ commerce:route }}`

Provides a route helper for Commerce routes. The key matches up with the configuration values.

```html
{{ commerce:route key='products.index' }}
```

#### `{{ commerce:categories }}`

Provides an array of product categories, you can find more documentation here.

#### `{{ commerce:products }}`

Provides an array of products, you can find more documentation here.

### Form Endpoints

On the front-end, Commerce uses lots of form request to do things like adding to the user's cart, redeeming a coupon and processing an order. Here's a list of the form endpoints that we provide, we'll add more detailed documentation on them later.

* `/cart` - Adds an item to the user's cart
* `/cart/clear` - Clears the user's cart
* `/cart/delete` - Removes an item from the user's cart
* `/checkout` - Processes the user's information, charges the customer and creates an order

## Widgets

Commerce provides a few widgets that you can add to the Dashboard of your Control Panel that displays key store information at a glance.

* `new_customers`
* `recent_orders`

You can add them to your Dashboard, by updating the list in your `cp.php` config file.

```php
<?php

return [
    ...

    'widgets' => [
        'new_customers',
        'recent_orders',
    ],
    
    ...
];
```

## Permissions

You might want certain people to only be able to access a certain part of Commerce. For example, you might want your marketing team to only access sales or products or you might want your fulfilment staff to only be able to access orders and customers. Commerce allows for this.

When choosing roles for Roles in Statamic, you can add granular control over what your users can access.

## Resources

* [Commerce Discord for testers](https://discord.gg/P3ACYf9)
* [Contributors Guide](./CONTRIBUTING.md)
* [Github Issues](https://github.com/damcclean/commerce/issues)

## Security

If you discover any security related issues, please email [security@doublethree.digital](mailto:security@doublethree.digital) instead of using the issue tracker.

## Credits

- [Duncan McClean](https://github.com/damcclean)
- [All Contributors](../../contributors)
