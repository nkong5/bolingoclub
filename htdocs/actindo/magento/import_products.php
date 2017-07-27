<?php
/**
 * import products
 *
 * actindo Faktura/WWS connector
 * 
 * @package actindo
 * @author  atlantis media GmbH <info@atlantismedia.de>
 * @version $Revision: 1.1 $
 * @copyright Copyright (c) 2009, atlantis media GmbH (Haferweg 26, 22769 Hamburg, Deutschland)
*/


function import_product($request) {

	$response = new ShopProductCreateUpdateResponse();

	for($product_index=0; $product_index < $request->products_size(); $product_index++) {
		$product = &$request->products($product_index);

		$result = $response->add_result();
		$result->set_index($product_index);
		$result->set_art_nr($product->art_nr());
		$ref = $product->reference();
		if(!is_null($ref))
      		$result->set_reference($ref);

		 __import_single_product( $product, $result );
	}

	$response->set_count($product_index);
	return $response;
	
}


function __import_single_product(&$product, &$result) {	
	
	$p = new Mage_Catalog_Model_Product();
	$art_id = $p->getIdBySku($product->art_nr()); 
	
	unset($MageResult);
	$shop_art = $product->shop();
	if(!is_object($shop_art)) {
		$result->set_ok(FALSE);
		$result->set_errno(EINVAL);
		$result->set_error("Shop-Artikeldaten nicht gesetzt oder Fehler bei der Übermittlung");
		return;
	}
 	
	if($product->mwst() == 7)
		$tax_class_id = 2;
	elseif($product->mwst() == 19)
		$tax_class_id = 4;
	else
		$tax_class_id = 0;

	
	if($art_id == FALSE) { //Insert
		
		//Attributsets holen
		$entityType = Mage::getModel('catalog/product')->getResource()->getEntityType();
		$collection = Mage::getResourceModel('eav/entity_attribute_set_collection')
			->setEntityTypeFilter($entityType->getId());
		$attributSetResult = array();
		foreach ($collection as $attributeSet) {
			$attributSetResult[] = array(
				'set_id' => $attributeSet->getId(),
				'name'   => $attributeSet->getAttributeSetName()
			);
		}
		
		$p = Mage::getModel('catalog/product');
		$p->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID)
			->setAttributeSetId($attributSetResult[0]['set_id'])
			->setPrice($product->grundpreis())
			->setSku($product->art_nr())
			->setName($product->art_name())
			->setTypeId('simple')
			->setDescription($product->description())
			->setShortDescription(' ')
			->setWeight($product->weight())
			->setTaxClassId($tax_class_id)
			->setManufacturer($shop_art->manufacturers_id())
			->setStatus($shop_art->products_status() ? 1 : 2)
			->save();
			
		$p_id = $p->getId();
		
		if(!$p_id) {
			$result->set_ok( FALSE );
			$result->set_errno( EIO );
			$result->set_error( "Fehler beim einfügen/ändern des Artikels." );
			return;
		}
		
	} else { //Update

		$p = Mage::getModel('catalog/product')->load($art_id);
		$p->setPrice($product->grundpreis())
			->setSku($product->art_nr())
			->setName($product->art_name())
			->setWeight($product->weight())
			->setTaxClassId($tax_class_id)
			->setManufacturer($shop_art->manufacturers_id())
			->setStatus($shop_art->products_status() ? 1 : 2)
			->save(); 		
		$p_id = $p->getId();
		
		if($p_id) {
			try {
				$stockAllow = Mage::getModel('cataloginventory/stock_item')->loadByProduct($p_id);
				$stockAllow->manage_stock = 1;
				$stockAllow->save();
			} catch(Exception $e) {
				
			}
		}
		
		if(!$p_id) {
			$result->set_ok( FALSE );
			$result->set_errno( EIO );
			$result->set_error( "Fehler beim einfügen/ändern des Artikels." );
			return;
		}
		
	}

	$result->set_ok( TRUE );

}


//TODO noch nicht implementiert für magento
function _do_import_attributes( $art_oxid, &$product, &$result )
{
  $langcode_to_id = array_flip( get_language_id_to_code() );

  $attributes = $product->attributes();
//  var_dump($attributes);
  if( !is_object($attributes) )
    return TRUE;

  $names = array();
  for( $i=0; $i<$attributes->names_size(); $i++ )
  {
    $attr_name = $attributes->names( $i );
    $names[$attr_name->name_id()] = array();
    for( $j=0; $j<$attr_name->translation_size(); $j++ )
    {
      $xlation = $attr_name->translation( $j );
      if( !isset($langcode_to_id[$xlation->language_code()]) )
        continue;
      $lang_id = $langcode_to_id[$xlation->language_code()];
      $names[$attr_name->name_id()][$lang_id] = $xlation->name();
    }
  }

  $values = array();
  for( $i=0; $i<$attributes->values_size(); $i++ )
  {
    $attr_value = $attributes->values( $i );
    $values[$attr_value->name_id()][$attr_value->value_id()] = array();
    for( $j=0; $j<$attr_value->translation_size(); $j++ )
    {
      $xlation = $attr_value->translation( $j );
      if( !isset($langcode_to_id[$xlation->language_code()]) )
        continue;
      $lang_id = $langcode_to_id[$xlation->language_code()];
      $values[$attr_value->name_id()][$attr_value->value_id()][$lang_id] = $xlation->name();
    }
  }
//  var_dump($values);

  $res = act_db_query( "SELECT `oxid` FROM `oxarticles` WHERE `oxparentid`='".esc($art_oxid)."'" );
  while( $child_art = act_db_fetch_assoc($res) )
  {
    $art = new oxArticle();
    $art->delete( $child_art['oxid'] );
  }
  act_db_free( $res );

  $res = act_db_query( "SELECT * FROM `oxarticles` WHERE `oxid`='".esc($art_oxid)."'" );
  $parent_art_data = act_db_fetch_assoc( $res );
  foreach( $langcode_to_id as $language_id )
    unset( $parent_art_data[_actindo_get_lang_field('oxvarname', $language_id)] );
  act_db_free( $res );

  $res = TRUE;
  $oxvarname = array();
  $oxvarnamedone = FALSE;
  for( $i=0; $i<$attributes->combination_advanced_size(); $i++ )
  {
    $comb_adv = $attributes->combination_advanced( $i );
    $pg0 = array();
    $oxvarselect = array();

    for( $combidx=0; $combidx<$comb_adv->combination_size(); $combidx++ )
    {
      $comb = $comb_adv->combination( $combidx );
      if( !$oxvarnamedone )
      {
        foreach( $names[$comb->name_id()] as $_langid => $_val )
          $oxvarname[$_langid][] = $_val;
      }
      foreach( $values[$comb->name_id()][$comb->value_id()] as $_langid => $_val )
        $oxvarselect[$_langid][] = $_val;
    }
    $oxvarnamedone = TRUE;

    for( $j=0; $j<$comb_adv->preisgruppen_size(); $j++ )
    {
      $pg = $comb_adv->preisgruppen( $j );
      if( $pg->preisgruppe() != 0 )
        continue;
      $pg0 = array( 'grundpreis'=>$pg->grundpreis(), 'is_brutto'=>$pg->is_brutto() );
    }

    $data = $comb_adv->data();
    if( is_object($data) )
      $products_status = $data->products_status();
    else
      $products_status = 1;

    $child_art_data = $parent_art_data;
    $child_oxid = oxUtilsObject::getInstance()->generateUID();

    $child_art_data['oxid'] = $child_oxid;
    $child_art_data['oxparentid'] = $art_oxid;
    $child_art_data['oxactive'] = $products_status ? 1 : 0;
    $child_art_data['oxartnum'] = $comb_adv->art_nr();
    $child_art_data['oxprice'] = $pg0['grundpreis'];
    $child_art_data['oxthumb'] = 'nopic.jpg';
    $child_art_data['oxicon'] = 'nopic_ico.jpg';
    $child_art_data['oxsort'] = $i+1;
    for( $imgidx=1; $imgidx<=$GLOBALS['myConfig']->getConfigParam( 'iPicCount' ); $imgidx++ )
      $child_art_data['oxpic'.$imgidx] = 'nopic.jpg';
    for( $imgidx=1; $imgidx<=$GLOBALS['myConfig']->getConfigParam( 'iZoomPicCount' ); $imgidx++ )
      $child_art_data['oxzoom'.$imgidx] = 'nopic.jpg';

    foreach( $oxvarselect as $_langid => $_val )
    {
      $_val = join( '|', $_val );
      $child_art_data[_actindo_get_lang_field('oxvarselect', $_langid)] = $_val;
    }

    $oxartextends_array = array();
    if( is_object($attribute_product=$comb_adv->shop_product()) )
      _do_import_descriptions_step1( $attribute_product, $child_art_data, $oxartextends_array, $langcode_to_id );

    if( !count($oxartextends_array) )
    {
      $res &= act_db_query( $q="REPLACE INTO `oxartextends` SET `oxid`='".esc($child_oxid)."'" );
    }
    else
    {
      // descriptions, part 2 of 2
      $set = construct_set( $oxartextends_array, 'oxartextends' );
      $res &= act_db_query( $q="UPDATE `oxartextends` ".$set['set']." WHERE `oxid`='".esc($child_oxid)."'" );
      if( !act_affected_rows() && act_db_get_single_row("SELECT COUNT(*) FROM `oxartextends` WHERE `oxid`='".esc($child_oxid)."'") == 0 )
        $res &= act_db_query( $q="INSERT INTO `oxartextends` ".$set['set'].", `oxid`='".esc($child_oxid)."'" );
    }

    $set = construct_set( $child_art_data, 'oxarticles' );
    $res &= act_db_query( $q="INSERT INTO `oxarticles` ".$set['set'] );

  }  // for( $i=0; $i<$attributes->combination_advanced_size(); $i++ )

  if( !$res )
  {
    $result->set_ok( FALSE );
    $result->set_errno( EIO );
    $result->set_error( "Fehler beim einfügen des Varianten-Artikels in die Tabelle 'oxarticles'" );
    return FALSE;
  }


  $add_fields_to_parent = array();
  foreach( $oxvarname as $_langid => $_val )
  {
    $_val = join( '|', $_val );
    $add_fields_to_parent[_actindo_get_lang_field('oxvarname', $_langid)] = $_val;
  }
  $add_fields_to_parent['oxvarcount'] = $attributes->combination_advanced_size();
  $set = construct_set( $add_fields_to_parent, 'oxarticles' );
  $res = act_db_query( "UPDATE `oxarticles` ".$set['set']." WHERE `oxid`='".esc($art_oxid)."'" );
  if( !$res )
  {
    $result->set_ok( FALSE );
    $result->set_errno( EIO );
    $result->set_error( "Fehler beim ändern des Auswahl-Namens in der Tabelle 'oxarticles'" );
    return FALSE;
  }


  return TRUE;
}

//TODO noch nicht implementiert für magento
function _do_import_all_categories( $art_oxid, &$product, &$result )
{
  $shop_art = $product->shop();
  
  $res = act_db_query( "DELETE FROM `oxobject2category` WHERE `oxobjectid`='".esc($art_oxid)."'" );
  if( !$res )
  {
    $result->set_ok( FALSE );
    $result->set_errno( EIO );
    $result->set_error( "Fehler beim löschen der alten Kategoriezuordnungen aus der Tabelle 'oxobject2category'" );
    return FALSE;
  }

  $all_cats = array( $product->categories_id() );
  for( $i=0; $i<$shop_art->all_categories_size(); $i++ )
  {
    $cat = $shop_art->all_categories( $i );
    $cat = $cat->categories_id();
    if( empty($cat) )
      continue;
    $all_cats[] = $cat;
  }
  $all_cats = array_unique( $all_cats );

  $res = TRUE;
  $no_pri_marker = 0;
  foreach( $all_cats as $_i => $cat )
  {
    $primary = $product->categories_id() == $cat ? 0 : $no_pri_marker+=10;
    $o2c_oxid = oxUtilsObject::getInstance()->generateUID();
    $set = construct_set( array(
      'oxid' => $o2c_oxid,
      'oxobjectid' => $art_oxid,
      'oxcatnid' => $cat,
      'oxpos' => 0,
      'oxtime' => $primary,
    ), 'oxobject2category' );
    $res &= act_db_query( $q="INSERT INTO `oxobject2category` ".$set['set'] );
  }
  if( !$res )
  {
    $result->set_ok( FALSE );
    $result->set_errno( EIO );
    $result->set_error( "Fehler beim Einfügen der Kategoriezuordnungen in die Tabelle 'oxobject2category'" );
    return FALSE;
  }

  return TRUE;
}


function product_update_stock($request) {
	
	$response = new ShopLagerUpdateResponse();

	$count = (int)$request->arts_size();
	for($k = 0; $k < $count; $k++) {
		
		$art = $request->arts($k);
		$result = $response->add_result();
		$result->set_index($k);
		$result->set_art_nr($art->art_nr());
		$result->set_reference($art->reference());
	
		$product = Mage::getModel('catalog/product'); 
		$p_current = $product->getIdBySku($art->art_nr()); 

		if($p_current == '') {
			$result->set_ok(FALSE);
			$result->set_errno(ENOENT);
			$art_nr = $art->art_nr();
			$result->set_error("Artikel '{$art_nr}' nicht gefunden.");
			continue;
		}

		$product->load($p_current);

		$stockAllow = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
		$stockAllow->manage_stock = 1;
		$stockAllow->save();
		unset($stockAllow);
		
		$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
		
		$stock->manage_stock = 1;
		$stock->qty = (float)$art->l_bestand();

		if($art->l_bestand() > 0)
			$stock->is_in_stock = 1;
		else
			$stock->is_in_stock = 0;
		
		$stock->save();

		unset($product);
		unset($stock);

    	$result->set_ok( TRUE );
    	
	}

	return $response;
	
}


//TODO noch nicht implementiert für magento
function product_delete( $request )
{
  $response = new ShopProductDeleteResponse();

  for( $i=0; $i<$request->products_size(); $i++ )
  {
    $prod = $request->products( $i );
    $result = $response->add_result();
    $result->set_index( $i );
    $result->set_art_nr( $prod->art_nr() );
    $result->set_reference( $prod->reference() );

    $oxid = act_db_get_single_row( "SELECT `oxid` FROM `oxarticles` WHERE `oxartnum`='".esc($prod->art_nr())."'" );
    if( !is_string($oxid) )
    {
      $result->set_ok( FALSE );
      $result->set_errno( ENOENT );
      $art_nr = $prod->art_nr();
      $result->set_error( "Artikel '{$art_nr}' nicht gefunden." );
      continue;
    }

    $result->set_ok( TRUE );
  }

  return $response;
}
?>