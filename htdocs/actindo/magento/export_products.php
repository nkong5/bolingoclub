<?php
/**
 * export products
 *
 * actindo Faktura/WWS connector
 * 
 * @package actindo
 * @author  atlantis media GmbH <info@atlantismedia.de>
 * @version $Revision: 1.3 $
 * @copyright Copyright (c) 2009, atlantis media GmbH (Haferweg 26, 22769 Hamburg, Deutschland)
*/


function export_products_count() {
	
	$response = new ShopProductCountResponse();

	$productItemsCollection = Mage::getModel('catalog/product')->getCollection();
	$response->set_product_count((int)count($productItemsCollection));

	return $response;
  
}

if(!function_exists('datetime_to_timestamp')) {
	function datetime_to_timestamp($date) {
		preg_match( '/(\d+)-(\d+)-(\d+)\s+(\d+):(\d+)(:(\d+))/', $date, $date );
		if( (!((int)$date[1]) && !((int)$date[2]) && !((int)$date[0])) )
			return -1;
		return mktime( (int)$date[4], (int)$date[5], (int)$date[7], (int)$date[2], (int)$date[3], (int)$date[1] );
	}
}


function __do_export_products($just_list=TRUE, $search_request, &$response) {

	$filter = $search_request->toArray();

	if($filter['filter'][0]['field'] == 'products_id' && isset($filter['filter'][0]['data']['value'])) {
		$products[] = Mage::getModel('catalog/product')->load($filter['filter'][0]['data']['value']);
	} else {
		$products = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');
	}

	foreach($products as $product) {
		$cnt++;
		
		$art = $response->add_products();

		$art->set_products_id($product->entity_id);
		$art->set_grundpreis($product->price);
		$art->set_is_brutto(true);
		
		
		if(strpos($product->category_ids,",")===FALSE) {
			$art->set_categories_id($product->category_ids);
		} else {
			$art->set_categories_id(10);
		}
		
		if($product->status == 1)
			$status = 1;
		else
			$status = 2;
		$art->set_products_status($status);
		
		$created = datetime_to_timestamp($product->created_at);
		$modified = datetime_to_timestamp($product->updated_at);
		$art->set_created($created > 0 ? $created : $modified);
		$art->set_last_modified($modified);
		$art->set_art_nr($product->sku);
		$art->set_art_name($product->name);

		if($just_list)
			continue;

		$shop_art = new ArtikelShopData();

		if($product->tax_class_id == 2)
			$mwst = 7;
		elseif($product->tax_class_id == 4)
			$mwst = 19;
		else
			$mwst = 0;
		$art->set_mwst($mwst);
		
		$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
		$art->set_l_bestand($stock->qty);
		$art->set_weight($product->weight);
		$art->set_weight_unit(ArtikelWeightUnit::g);

		$shop_art->set_products_status($product->status);
		$shop_art->set_products_sort($product->sku);
		$shop_art->set_manufacturers_id($product->manufacturer);

		$shop_art->set_products_weight((float)$product->weight);

 		if($product->type_id == 'downloadable')
 			$shop_art->set_nonmaterial(true);
 		else
 			$shop_art->set_nonmaterial(false);

		//$shop_art->set_length($prod['oxlength']); --> diese 3 attribute gibts in magento nicht
		//$shop_art->set_width($prod['oxwidth']);
		//$shop_art->set_height($prod['oxheight']);

		// categories
		_do_export_all_categories($product, $art, $shop_art);

		// descriptions
		_do_export_descriptions($product, $art);

		// preisgruppen
		//_do_export_pricegroups($prod, $art, $shop_art);

		// attributes (varianten)
		//_do_export_attributes($prod, $art);

		// xselling
		//_do_export_xselling($prod, $art, $shop_art);

		// images
		_do_export_images($product, $art);

		// content
		//_do_export_content($prod, $art, $shop_art);

		// Attributes [Zusatzfelder!]
		_do_export_properties($product, $art, $shop_art); //vorbereitet

		$art->set_shop($shop_art);
	}

	$response->set_count( $cnt );

	return array( 'ok' => TRUE );
  
}


function export_products_list($request) {
	
	$response = new ShopProductListResponse();
	$search_request = $request->search_request();

	$p = $search_request->sortColName();
	if( empty($p) )
		$search_request->set_sortColName( 'oxartnum' );

	$p = $search_request->sortOrder();
	if( empty($p) )
		$search_request->set_sortOrder( 'ASC' );

	$res = __do_export_products( TRUE, $search_request, $response );
	if( !$res['ok'] )
		return $res;

  return $response;
  
}



function export_products($request) {
	
  $response = new ShopProductGetResponse();
  $search_request = $request->search_request();

  $p = $search_request->sortColName();
  if( empty($p) )
    $search_request->set_sortColName( 'oxartnum' );

  $p = $search_request->sortOrder();
  if( empty($p) )
    $search_request->set_sortOrder( 'ASC' );

  $res = __do_export_products( FALSE, $search_request, $response );
  if( !$res['ok'] )
    return $res;

  return $response;
  
}


//TODO noch nicht implementiert für magento
function _do_export_attributes( &$prod, &$art )
{
  if( empty($prod['oxvarname']) && empty($prod['oxvarname_1']) && empty($prod['oxvarname_2']) )
  {
    // no attributes
    return array( 'ok'=> TRUE );
  }

  $lang_id_to_code = get_language_id_to_code();
  $attributes = new ArtikelAttributes();
  $attributes->set_other_combinations_dont_exist( TRUE );

  $attribute_names = array();
  foreach( $lang_id_to_code as $language_id => $code )
  {
    $var_name = $prod[$p=_actindo_get_lang_field('oxvarname', $language_id)];
    if( empty($var_name) )
      continue;

    $names = split( '\|', $var_name );
    foreach( $names as $_i => $_name )
    {
      $attribute_names[$_i][$code] = $_name;
    }
  }
  foreach( $attribute_names as $_i => $tmp )
  {
    $name = $attributes->add_names();
    $name->set_name_id( $prod['oxid'].'__'.$_i );
    foreach( $tmp as $_code => $_name )
    {
      $xlation = $name->add_translation( );
      $xlation->set_language_code( $_code );
      $xlation->set_name( $_name );
    }
  }
  unset( $tmp );

  $children = array();
  $res = act_db_query( "SELECT * FROM `oxarticles` WHERE `oxparentid`='".esc($prod['oxid'])."' ORDER BY `oxsort`" );
  while( $child = act_db_fetch_assoc($res) )
  {
    $children[] = $child;
  }
  act_db_free( $res );

  $attribute_values = array();
  $attribute_values_to_child = array();
  foreach( $children as $_child_i => $child )
  {
    $newval = array();
    foreach( $lang_id_to_code as $language_id => $code )
    {
      $var_name = $child[_actindo_get_lang_field('oxvarselect', $language_id)];
      if( empty($var_name) )
        continue;

      $values = split( '\|', $var_name );
      foreach( $values as $_name_id => $_name )
      {
        $newval[$_name_id][$code] = $_name;
      }
    }

    foreach( $newval as $_name_id => $_descr )
    {
      $found = 0;
      foreach( $attribute_values[$_name_id] as $_val_id => $tmp )
      {
        if( $tmp == $_descr )
        {
          $found++;
          break;
        }
      }
      if( !$found )
      {
        $_val_id = count($attribute_values[$_name_id]);
        $attribute_values[$_name_id][$_val_id] = $_descr;
      }
      $attribute_values_to_child[$child['oxid']][] = array( $_name_id, $_val_id );
    }
  }

//  var_dump($attribute_values);
//  var_dump($attribute_values_to_child);

  foreach( $attribute_values as $_name_id => $tmp1 )
  {
    foreach( $tmp1 as $_value_id => $tmp )
    {
      $value = $attributes->add_values();
      $name_id = $prod['oxid'].'__'.$_name_id;
      $value_id = 'V'.$_name_id.'__'.$_value_id;
      $value->set_name_id( $name_id );
      $value->set_value_id( $value_id );
      foreach( $tmp as $_code => $_name )
      {
        $xlation = $value->add_translation( );
        $xlation->set_language_code( $_code );
        $xlation->set_name( $_name );
      }

      $combination_simple = $attributes->add_combination_simple();
      $combination_simple->set_value_id( $value_id );
      $combination_simple->set_name_id( $name_id );
      $combination_simple->set_attributes_model( '' );
    }
  }

  foreach( $children as $_child_i => $child )
  {
    $ca = $attributes->add_combination_advanced();
    $ca->set_art_nr( $child['oxartnum'] );
    $ca->set_l_bestand( $child['oxstock'] );

    $combi = $attribute_values_to_child[$child['oxid']];
    foreach( $combi as $_c )
    {
      $comb = $ca->add_combination();
      $comb->set_name_id( $prod['oxid'].'__'.$_c[0] );
      $comb->set_value_id( 'V'.$_c[0].'__'.$_c[1] );
    }

    $data = new ArtikelAttributesCombinationAdvanced_CombinationAdvancedData();
    $data->set_products_status( $prod['oxactive'] );
    $ca->set_data( $data );
  }

  $art->set_attributes( $attributes );
  return array( 'ok' => TRUE );
}


//TODO noch nicht implementiert für magento
function _do_export_content( &$prod, &$art, &$shop_art )
{
  $lang_id_to_code = get_language_id_to_code();
  $res1 = act_db_query( $sql="SELECT * FROM `oxmediaurls` WHERE `oxobjectid`='".esc($prod['oxid'])."'" );
  while( $row=act_db_fetch_assoc($res1) )
  {
    foreach( $lang_id_to_code as $language_id => $code )
    {
      $content_name = $row[_actindo_get_lang_field('oxdesc', $language_id)];
      if( empty($content_name) )
        continue;

      $ct = $art->add_content();
      if( $row['oxisuploaded'] > 0 )
      {
        $ct->set_type( ShopProduct_ArtikelContentType::file );

        $cmp = parse_url( $row['oxurl'] );
        $path = $cmp['path'];
        $ct->set_content_file_name( basename($path) );

        $filepath = strtr( $row['oxurl'], array('https'=>'http', $GLOBALS['myConfig']->getConfigParam('sShopURL') => $GLOBALS['myConfig']->getConfigParam('sShopDir')) );
        if( file_exists($filepath) )
        {
          $content = file_get_contents($filepath);
          $ct->set_content( $content );
          $ct->set_content_file_md5( md5($content) );
          $ct->set_content_file_size( strlen($content) );
          unset( $content );
        }
        else
        {
          // if the calculated path does not exist, get using URL!
          $c1 = curl_init( $row['oxurl'] );
          curl_setopt( $c1, CURLOPT_RETURNTRANSFER, TRUE );
          curl_setopt( $c1, CURLOPT_BINARYTRANSFER, TRUE );
          $content = curl_exec( $c1 );
          curl_close( $c1 );
          $ct->set_content( $content );
          $ct->set_content_file_md5( md5($content) );
          $ct->set_content_file_size( strlen($content) );
          unset( $content );
        }
      }
      else
      {
        $ct->set_type( ShopProduct_ArtikelContentType::link );
        $ct->set_content( $row['oxurl'] );
      }
      $ct->set_language_code( $code );
      $ct->set_content_name( $content_name );
    }
  }
  act_db_free( $res1 );

  return array( 'ok' => TRUE );
}


function _do_export_properties(&$product, &$art, &$shop_art) {
	
	$collection = Mage::getResourceModel('eav/entity_attribute_collection')
		->setEntityTypeFilter( Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId() );
		//->addVisibleFilter(); => Magento 1.4 kennt die Funktion nicht mehr

	//Felder, die nicht übertragen werden sollen (Magento liefert alle Artikelinformationen, wir benötigen aber nur zusätzliche Felder, die noch nicht anderweitig übertragen wurden)
	$filterArray = array('description','cost','custom_design','enable_googlecheckout','gallery','gift_message_available','image','media_gallery','meta_description','meta_keyword','meta_title','name','news_from_date','news_to_date','options_container','page_layout','price','price_view','short_description','sku','small_image','special_from_date','special_price','special_to_date','status','tax_class_id','theme','thumbnail','tier_price','url_key','visibility','');
		
	foreach ($collection as $attribute) {
		if(!in_array($attribute->attribute_code,$filterArray)) {
			$attributeCode = $attribute->attribute_code;
			$xs = $art->add_properties();
			$xs->set_field_id($attributeCode);
			$xs->set_language_code('de');
			$attributeValue = $product->$attributeCode;
			if(strlen($attributeValue)>0) {
				if($attribute->attribute_code=='weight') {
					$xs->set_field_value(round($attributeValue,2));
				} else {
					if($attribute->frontend_input=='select')
						$xs->set_field_value($product->getResource()->getAttribute($attributeCode)->getFrontend()->getValue($product));
					else
						$xs->set_field_value($attributeValue);
				}
			}
		}
	}
  
	return array( 'ok' => TRUE );
	
}


//TODO noch nicht implementiert für magento
function _do_export_xselling( &$prod, &$art, &$shop_art )
{
  $res1 = act_db_query( $sql="SELECT a.`oxartnum`, o2a.`oxsort` FROM `oxaccessoire2article` AS o2a, `oxarticles` AS a WHERE a.oxid=o2a.oxobjectid AND o2a.`oxarticlenid`='".esc($prod['oxid'])."' ORDER BY o2a.`oxsort` ASC" );
  while( $row=act_db_fetch_assoc($res1) )
  {
    $xs = $shop_art->add_xselling();
    $xs->set_group_id( 1 );
    $xs->set_art_nr( $row['oxartnum'] );
    $xs->set_sort_order( $row['oxsort'] );
  }
  act_db_free( $res1 );

  $res1 = act_db_query( $sql="SELECT a.`oxartnum`, o2a.`oxsort` FROM `oxobject2article` AS o2a, `oxarticles` AS a WHERE a.oxid=o2a.oxobjectid AND o2a.`oxarticlenid`='".esc($prod['oxid'])."' ORDER BY o2a.`oxsort` ASC" );
  while( $row=act_db_fetch_assoc($res1) )
  {
    $xs = $shop_art->add_xselling();
    $xs->set_group_id( 2 );
    $xs->set_art_nr( $row['oxartnum'] );
    $xs->set_sort_order( $row['oxsort'] );
  }
  act_db_free( $res1 );

  if( !empty($prod['oxbundleid']) )
  {
    $res1 = act_db_query( "SELECT `oxartnum` FROM `oxarticles` WHERE `oxid`='".esc($prod['oxbundleid'])."'" );
    $row = act_db_fetch_assoc($res1);
    act_db_free( $res1 );

    $xs = $shop_art->add_xselling();
    $xs->set_group_id( 3 );
    $xs->set_art_nr( $row['oxartnum'] );
    $xs->set_sort_order( 0 );
  }

  return array( 'ok' => TRUE );
}

//TODO noch nicht implementiert für magento
function _do_export_pricegroups( &$prod, &$art, &$shop_art )
{
  $pgs = _act_get_pricegroups_to_field( );
  foreach( $pgs as $_pgid => $_field )
  {
    if( is_null($prod[$_field]) )
      continue;

    $pg = $art->add_preisgruppen();
    $pg->set_preisgruppe( $_pgid );
    $pg->set_is_brutto( $art->is_brutto() );
    $pg->set_grundpreis( (float)$prod[$_field] );

    // TODO: kann oxid preisstaffeln bei preisgruppen ??
  }

  return array( 'ok' => TRUE );
}


function _do_export_all_categories(&$product, &$art, &$shop_art) {
  
	$catIDs = explode(',',$product->category_ids);
	foreach($catIDs as $category) {	
		$cat = $shop_art->add_all_categories();
		$cat->set_categories_id($category);
	}

	return array( 'ok' => TRUE );
	
}


function _do_export_descriptions(&$product, &$art) {

	$desc = $art->add_description();
	$desc->set_language_code('de');
	$desc->set_language_id(1);
	$desc->set_products_name($product->name);
	$desc->set_products_description(strip_tags($product->description));
	$desc->set_products_short_description(strip_tags($product->short_description));
	$desc->set_products_keywords($product->meta_keyword);
	$desc->set_products_meta_keywords($product->meta_keyword);

}


function _do_export_images(&$product, &$art) {
	
	$_media = $product->getMediaGalleryImages();
	$piccount = count($_media);
	
	$i=0;
	foreach($_media as $photo) {
		
		$i++;
		$img = $art->add_images();
		$img->set_image_nr($i);
		$img->set_image_name(substr($photo->getFile(),5));
		$img->set_image_type('image/jpeg');
		$img->set_image_subfolder('');
		$content = file_get_contents($_SERVER['DOCUMENT_ROOT'].MAGENTO_BASEPATH.'media/catalog/product'.$photo->getFile());
		$img->set_image_md5(md5($content));
		$img->set_image($content);
		unset($content);
	}
	
}
?>