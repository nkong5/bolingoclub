<?php 
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_MeigeewidgetsUnique_Model_Templates
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'meigee/meigeewidgetsunique/grid.phtml', 'label'=>'Grid'),
            array('value'=>'meigee/meigeewidgetsunique/list.phtml', 'label'=>'List'),
			array('value'=>'meigee/meigeewidgetsunique/list_small.phtml', 'label'=>'Small List'),
            array('value'=>'meigee/meigeewidgetsunique/slider.phtml', 'label'=>'Slider')
        );
    }

}