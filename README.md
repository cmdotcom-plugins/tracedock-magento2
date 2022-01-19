# Introduction


Due to an increase in adblockers, browsers with tracking prevention and cookie legislation, ecommerce websites miss 
up to 30% of their marketing attribution data. TraceDock is a first party data collection service working in parallel to 
Google Analytics and connecting your conversions to the Facebook Conversion API to improve the data that you 
measure from your website visitors.

The goal of this package is to simplify the configuration of serverside transaction tracking for customers with Magento2.

The package includes a Magento 2 module and will:
1. Setup the identify event based on the quoteId of the checkout.
2. Forward invoice data to the TraceDock endpoint to connect serverside transactions.

https://docs.tracedock.com/configuration/server-side-transaction-tracking/ for docs on the serverside transaction tracking.

Note: we assume that customers have implemented the basic setup of TraceDock, including adding a DNS record and adding the 
TraceDock code to the template of the website, as found in https://docs.tracedock.com//installation/start

This readme contains a step-by-step description of how you can install the module in your Magneto2 environment.

## Installation

To install this module using composer run:

```bash
composer require tracedock/module-transaction-tracking
```

When the module is installed run the Magento 2 command:
```bash
bin/magento setup:upgrade
```

## Configuration

In the backend of Magento 2, the TraceDock configurations can be found under:
`Stores > Configuration > Tracedock > General`

To enable TraceDock webhooks, set the `enabled` field to `Yes` and configure the `Tracedock API endpoint` with the URL 
provided by TraceDock. It can be found in the transaction event under the
[serverside_events](https://portal.tracedock.com/serverside_events) tab of the portal. 

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

You can debug these events within the TraceDock portal. 
Go to [serverside_events](https://portal.tracedock.com/serverside_events) tab in the TraceDock portal.
Press on the three dots (...) after the event, and you will automatically filter the transactions in the live event view.

## Questions or support?

If you have any questions, please contact us on [support@tracedock.com](mailto:support@tracedock.com). We love to help.

## Known issues
No known issues.

## Changelog
See the [Changelog](CHANGELOG.md)

## Contribute

See the [Contribution Guidelines](CONTRIBUTE.md)