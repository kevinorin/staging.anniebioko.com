<?php
class VES_CheckoutDiscountCode_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Validate ajax request and redirect on failure
	 *
	 * @return bool
	 */
	protected function _expireAjax()
	{
		if (!$this->getOnepage()->getQuote()->hasItems()
				|| $this->getOnepage()->getQuote()->getHasError()
				|| $this->getOnepage()->getQuote()->getIsMultiShipping()) {
			$this->_ajaxRedirectResponse();
			return true;
		}
		$action = $this->getRequest()->getActionName();
		if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true)
				&& !in_array($action, array('index', 'progress'))) {
			$this->_ajaxRedirectResponse();
			return true;
		}
	
		return false;
	}
	
	
	protected function _ajaxRedirectResponse()
	{
		$this->getResponse()
		->setHeader('HTTP/1.1', '403 Session Expired')
		->setHeader('Login-Required', 'true')
		->sendResponse();
		return $this;
	}
	
	/**
	 * Get shipping method step html
	 *
	 * @return string
	 */
	protected function _getShippingMethodsHtml()
	{
		$layout = $this->getLayout();
		$update = $layout->getUpdate();
		$update->load('checkout_onepage_shippingmethod');
		$layout->generateXml();
		$layout->generateBlocks();
		$output = $layout->getOutput();
		return $output;
	}
	
	/**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current active quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }
	
	/**
	 * Get one page checkout model
	 *
	 * @return Mage_Checkout_Model_Type_Onepage
	 */
	public function getOnepage()
	{
		return Mage::getSingleton('checkout/type_onepage');
	}
	
	public function submitAction()
    {
    	if ($this->_expireAjax()) {
    		return;
    	}
    	if ($this->getRequest()->isPost()) {
    		$couponCode = trim($this->getRequest()->getPost('coupon_code'));
    		$result = array();
    		
    		$this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
			$this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
				->collectTotals()
				->save();
			if ($couponCode == $this->_getQuote()->getCouponCode()) {
				$result = array('error' => 0);
				$this->_getSession()->setStepData('discountcode', 'complete');
			}
			else {
				$result = array('error' => 1, 'message' => $this->__('Coupon code %s is not valid.', Mage::helper('core')->htmlEscape($couponCode)));
			}
    		
    		if(!$result['error']) {
    			$this->getOnepage()->getQuote()->getShippingAddress()->setCollectShippingRates(true);
    			 $result['goto_section'] = 'shipping_method';
                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml()
                    );
    		}else {
            	$result['goto_section'] = 'discountcode';
            }
    		
    		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    	}
    }
	public function indexAction(){
    	$this->loadLayout()->renderLayout();
    }
}