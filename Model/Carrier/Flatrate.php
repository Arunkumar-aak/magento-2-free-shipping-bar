<?php
namespace Arun\FreeShippingBar\Model\Carrier;

class Flatrate
{

    const XML_PATH_FREE_SHIPPING_SUBTOTAL = "free_shipping_bar/free_shipping_bar/minimum_order_amount";

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_checkoutSession = $checkoutSession;
        $this->_scopeConfig = $scopeConfig;
    }

    public function afterCollectRates(\Magento\OfflineShipping\Model\Carrier\Flatrate $flatRate, $result)
    {
        $scopeId = $this->_storeManager->getStore()->getId();

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;

        // Get MOA value from system configuration.
        $freeShippingSubTotal = $this->_scopeConfig->getValue(self::XML_PATH_FREE_SHIPPING_SUBTOTAL, $storeScope, $scopeId);

        // Get cart subtotal from checkout session.
        $baseSubTotal = $this->_checkoutSession->getQuote()->getBaseSubtotal();

        // Validate subtoal should be empty or Zero.
        if(!empty($baseSubTotal) && !empty($freeShippingSubTotal)) {

            if($baseSubTotal >= $freeShippingSubTotal) {
                return false;
            }
        }

        return $result;
    }
}