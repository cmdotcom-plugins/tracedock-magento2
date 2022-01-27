# Introduction


Due to an increase in the use of adblockers, browsers with tracking prevention and cookie legislation, e-commerce websites miss
up to 30% of their marketing attribution data. TraceDock is a first-party data collection service working in parallel to
Google Analytics and connecting your conversions to the Facebook Conversion API to improve the data that you
measure from your website visitors.

The goal of this package is to simplify the configuration of our Server-side Transaction Tracking service for customers with Magento2.

The package includes a Magento2 module and will:
1. Set up the identify event based on the quoteId of the checkout.
2. Forward invoice data to the TraceDock endpoint to connect serverside transactions.

See https://docs.tracedock.com/configuration/server-side-transaction-tracking/ for documentation about the Server-side Transaction Tracking.

Note: we assume that customers have implemented the basic setup of TraceDock, including adding a DNS record and adding the
TraceDock code to the template of the website, as found in https://docs.tracedock.com//installation/start.

This readme contains a step-by-step description of how you can install the module in your Magneto2 environment.

## Installation

To install this module using composer run (via github):

```bash
composer config repositories.tracedock.magento2 git git@github.com:cmdotcom-plugins/tracedock-magento2.git
composer require cmdotcom-plugins/tracedock-magento2.git main
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
php bin/magento cache:clean
```

When the module is installed run the Magento2 command:
```bash
bin/magento setup:upgrade
```

## Configuration

In the backend of Magento2, the TraceDock configurations can be found under:
`Stores > Configuration > Tracedock > General`

![TraceDock configuration screen](https://github.com/cmdotcom-plugins/tracedock-magento2/blob/main/static/tracedock_tab.png?raw=true)

* __Enable Module__ - With this function you can (temporarily) disable this tab.
* __Enable Production__ - If this tab is enabled, the variable `env` will be populated with `production`, which allows you to filter it out in TraceDock using the conditions.
* __API endpoint__ - The endpoint as found in the [serverside_events](https://portal.tracedock.com/serverside_events) tab of the TraceDock user portal.
* __Included Attribution__ - Attributions that will be included in forwarding data to TraceDock. 



## Extendability
This module uses the decorator pattern to allow for better maintainability and extension on the data that is being sent to TraceDock.

To add or change existing data a custom decorator can be added in the di.xml
of another module.

```
<type name="Tracedock\TransactionTracking\Model\Mapper">
    <arguments>
        <argument name="mapperDecorators" xsi:type="array">
            <item name="standard" xsi:type="object">Tracedock\TransactionTracking\Model\Mapper\CustomDecorator</item>
        </argument>
    </arguments>
</type>
```

Ensure the decorator is implementing the interface: `Tracedock\TransactionTracking\Api\MapperDecoratorInterface`


## Test events in the portal

After the module is installed and configured, all orders with the status will be automatically forwarded to TraceDock.

You can debug these events within the TraceDock user portal.
Go to [serverside_events](https://portal.tracedock.com/serverside_events) tab in the TraceDock user portal.
Press on the three dots (...) after the event, and you will automatically filter the transactions in the live event view.

## Questions or support?

If you have any questions, please contact us on [support@tracedock.com](mailto:support@tracedock.com). We love to help.

## Known issues
No known issues.

## Changelog
See the [Changelog](CHANGELOG.md)

## Contribute

See the [Contribution Guidelines](CONTRIBUTE.md)
