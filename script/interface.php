<?php

	require '../config.php';
	
	dol_include_once('/product/class/product.class.php');
	
	$put = GETPOST('put');
	
	switch ($put) {
		case 'change-line':
			
			$lineid=GETPOST('lineid');
			$label = GETPOST('label');
			$price = GETPOST('price');
			$element = GETPOST('element');
			
			if (empty($conf->global->PRODUCT_CODEPRODUCT_ADDON) || $conf->global->PRODUCT_CODEPRODUCT_ADDON == 'mod_codeproduct_leopard') $ref = 'FROMLINE-'.$lineid;
		
			$product = new Product($db);
			$product->ref = $ref;
			$product->label = $label;
			$product->price = $price;
			$product->status = 1;
			$product->status_buy = 1;
			$id = $product->create($user);
			
			if($id>0) {
				if($element == 'propal')$table='propaldet';
				else if($element == 'commande')$table='commandedet';
				
				$db->query("UPDATE ".MAIN_DB_PREFIX.$table." SET fk_product=".$id." WHERE rowid=".$lineid);
				var_dump($db);
			}
			
			echo $id;
			
			break;
		
	}
