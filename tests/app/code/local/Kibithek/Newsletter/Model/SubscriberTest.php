<?php


class Kibithek_Newsletter_Model_SubscriberTest extends Kibithek_PHPUnit_TestCase
{
    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Kibithek_Newsletter_Model_SubscriberTest', Mage::getModel('kibithek_newsletter/subscriber'));
    }


    /**
     * @test
     */
    public function subscribeCustomer ()
    {
        Mage::register('isSecureArea', true);
        $store = Mage::app()->getStore('de');
        $email = 'biz@bongkishiy.de';

        Mage::getModel('newsletter/subscriber')->loadByEmail($email)->delete();

        $subscriber = Mage::getModel('newsletter/subscriber');
        $subscriber->setStoreId($store->getId());

        $this->_deleteDummyCustomer($email);
        $customer = $this->_saveDummyCustomer($email);
        $customer->setIsSubscribed($customer::SUBSCRIBED_YES);

        $storeFrontendName = Mage::helper('kibithek_core')->getStoreFrontendName();
        $subscriber->setStoreFrontendName($storeFrontendName);
        $subscriber->setCustomerFirstname($customer->getFirstname());
        $subscriber->setCustomerLastname($customer->getLastname());

        $subscriberNew = $subscriber->subscribeCustomer($customer);

        $name = $subscriberNew->getSubscriberFullName();
        $this->assertNotEmpty($name);
    }

    private function _deleteDummyCustomer($email) {
        $customer = Mage::getModel("customer/customer");
        $store = Mage::app()->getStore('de');
        $customer->setStore($store);
        $customer->setWebsiteId($store->getWebsiteId());
        $customer->loadByEmail($email)->delete();
    }

    private function _saveDummyCustomer($email)
    {
        $customer = Mage::getModel("customer/customer");
        $store = Mage::app()->getStore('de');
        $customer->setStore(Mage::app()->getStore('de'));
        $customer->setWebsiteId($store->getWebsiteId());

        $customer->setPrefix('Herr');
        $customer->setFirstname('Max');
        $customer->setLastname('Mustermann');
        $customer->setEmail($email);

        $attrNewsletterLocationType = Mage::getModel(
            'kibithek_customer/entity_attribute_source_newsletterSubscriptionType'
        );
        $params['newsletter_subscription_type'] = $attrNewsletterLocationType::TYPE_FOOTER_VALUE;

        $customer->setNewsletterSubscriptionType($attrNewsletterLocationType::TYPE_FOOTER_VALUE);

        $random = substr(md5(microtime()),rand(0,100),40);
        $customer->setPasswordHash(md5($random));

        return $customer->save();
    }

}
