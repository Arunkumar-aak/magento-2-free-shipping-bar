<?php
namespace Arun\FreeShippingBar\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

class FreeShippingBar extends \Magento\Framework\View\Element\Template
{
    protected $scopeConfig;
    protected $checkoutSession;
    protected $priceHelper;

    const XML_PATH_IS_ENABLED = 'free_shipping_bar/free_shipping_bar/enabled';
    const XML_PATH_MINIMUM_ORDER_AMOUNT = 'free_shipping_bar/free_shipping_bar/minimum_order_amount';
    const XML_PATH_BACKGROUND_COLOR = 'free_shipping_bar/free_shipping_bar/background_color';
    const XML_PATH_TEXT_COLOR = 'free_shipping_bar/free_shipping_bar/text_color';

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        Session $checkoutSession,
        PriceHelper $priceHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfig = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
        $this->priceHelper = $priceHelper;
    }

    public function isEnabled()
    {
       
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_IS_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    public function getMinimumAmount()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MINIMUM_ORDER_AMOUNT, ScopeInterface::SCOPE_STORE);
    }

    public function getBackgroundColor()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_BACKGROUND_COLOR, ScopeInterface::SCOPE_STORE);
    }

    public function getTextColor()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TEXT_COLOR, ScopeInterface::SCOPE_STORE);
    }

    public function formatPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }

    public function getConfiguredMessage($messageCode, $cartTotal, $minimumOrderAmount)
    {
        $message = $this->scopeConfig->getValue('free_shipping_bar/free_shipping_bar/' . $messageCode, ScopeInterface::SCOPE_STORE);
        $remainingAmount = max(0, $minimumOrderAmount - $cartTotal);

        if (!empty($message)) {
            if (strpos($message, '{{minimum_order_amount}}') !== false) {
              $message = str_replace('{{minimum_order_amount}}', $this->formatPrice($minimumOrderAmount), $message);
            }
            if (strpos($message, '{{remaining_amount}}') !== false) {
              $message = str_replace('{{remaining_amount}}', $this->formatPrice($remainingAmount), $message);
            }
            if (strpos($message, '{{cart_total}}') !== false) {
              $message = str_replace('{{cart_total}}', $this->formatPrice($cartTotal), $message);
            }
          }

        return $message;
    }

    public function getFreeShippingMessage()
    {
        $minimumOrderAmount = $this->getMinimumAmount();
        $cartTotal = $this->checkoutSession->getQuote()->getSubtotal();

        if ($cartTotal == 0) {
            $message = $this->getConfiguredMessage('cart_is_empty_message', $cartTotal, $minimumOrderAmount);
        } elseif ($cartTotal < $minimumOrderAmount) {
            $message = $this->getConfiguredMessage('cart_value_less_than_minimum_message', $cartTotal, $minimumOrderAmount);
        } else {            
            $message = $this->getConfiguredMessage('success_message', $cartTotal, $minimumOrderAmount);
        }

       return $message;
    }

}