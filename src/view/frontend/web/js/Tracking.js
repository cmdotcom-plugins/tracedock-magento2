/*
* Magento does not contain a default userId,
* as such we use the quoteId to stitch with the browser session.
* For compatibility with the template installation we forward both
* fields to TraceDock endpoint.
*/

define(['jquery'], function($)
{
    return function(config)
    {
      if (window.checkoutConfig && window.checkoutConfig.quoteData) {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
          quoteId: window.checkoutConfig.quoteData.entity_Id,
          userId: window.checkoutConfig.quoteData.entity_Id
        })
      }
    };
});
