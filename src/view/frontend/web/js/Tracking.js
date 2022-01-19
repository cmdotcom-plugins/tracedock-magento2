define(['jquery'], function($)
{
    return function(config)
    {
      if (window.checkoutConfig && window.checkoutConfig.quoteData) {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({quoteId:window.checkoutConfig.quoteData.entity_Id})
      }
    };
});
