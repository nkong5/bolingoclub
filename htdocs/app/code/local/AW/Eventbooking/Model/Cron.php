<?php

class AW_Eventbooking_Model_Cron
{

    /**
     * Call every 0 * * * *
     * Uses for send reminder email to customer
     */
    public function eventReminder()
    {
        $now = new Zend_Date();
        /**@var AW_Eventbooking_Model_Resource_Event_Collection $eventCollection*/
        $eventCollection = Mage::getModel('aw_eventbooking/event')->getCollection()
            ->addPendingReminderEmailFilter()
            ->addEventAttributes()
        ;
        foreach ($eventCollection as $event) {
            $daysBefore = (int)$event->getData('day_count_before_send_reminder_letter');
            $eventDatetime = new Zend_Date($event->getData('event_start_date'), Varien_Date::DATETIME_INTERNAL_FORMAT);
            $eventDatetime->subDay($daysBefore);
            if ($now->compare($eventDatetime) === -1) {
                continue;
            }
            try {
                Mage::helper('aw_eventbooking/mailer')->sendReminderEmailForEvent($event);
                Mage::helper('aw_eventbooking/mailer')->sendReminderEmailForHolderByEvent($event);
            } catch (Exception $e) {
                Mage::logException($e);
            }
            $event->setData('is_reminder_send', 1);
            $event->save();
        }
    }
}
