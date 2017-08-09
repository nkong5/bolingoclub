<?php

class AW_Eventbooking_Helper_Product
{
    public function addProductFieldOptions(Mage_Catalog_Model_Product $product, $type, $id, $ticket)
    {
        $option = Mage::getModel('catalog/product_option');
        $option->setData(
            array(
                'option_id' => $id,
                'option_type_id' => $ticket->getId(),
                'type' => $type,
                'is_require' => 1,
                'sku' => $ticket->getData('sku'),
                'max_characters' => null,
                'file_extension' => null,
                'image_size_x' => '0',
                'image_size_y' => '0',
                'sort_order' => $ticket->getData('sort_order'),
                'default_title' => $ticket->getData('title'),
                'store_title' => $ticket->getData('title'),
                'title' => $ticket->getData('title'),
                'default_price' => $ticket->getData('price'),
                'default_price_type' => $ticket->getData('price_type'),
                'store_price' => $ticket->getData('price'),
                'store_price_type' => $ticket->getData('price_type'),
                'price' => $ticket->getData('price'),
                'price_type' => $ticket->getData('price_type'),
                'custom_field' => 1,
                'available_qty' => $ticket->getQty() - $ticket->getPurchasedTicketQty()
            )
        );

        $option->setProduct($product);
        $product->addOption($option);
        return $option;
    }
}