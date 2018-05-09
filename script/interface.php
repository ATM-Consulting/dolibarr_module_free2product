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
			$ref = GETPOST('ref');
			$product_type = GETPOST('product_type');
			$tva = GETPOST('tva');
			
			$base_new_ref = !empty($conf->global->FREE2PRODUCT_BASE_NEWREF) ? $conf->global->FREE2PRODUCT_BASE_NEWREF : 'FREELINE-';
			
			if (empty($ref) && (empty($conf->global->PRODUCT_CODEPRODUCT_ADDON) || $conf->global->PRODUCT_CODEPRODUCT_ADDON == 'mod_codeproduct_leopard')) {
				$ref = $base_new_ref.$lineid;
			} 
		
			$product = new Product($db);
			$product->type = $product_type;
			$product->ref = $ref;
			$product->label = $label;
			$product->libelle = $product->label; // @deprecated
			$product->price = $price;
			$product->tva_tx = $tva;
			$product->status = 1;
			$product->status_buy = 1;
			
			if (! empty($conf->barcode->enabled) && ! empty($conf->global->BARCODE_PRODUCT_ADDON_NUM)) {
				
				$module=strtolower($conf->global->BARCODE_PRODUCT_ADDON_NUM);
				$dirbarcode=array_merge(array('/core/modules/barcode/'),$conf->modules_parts['barcode']);
				foreach ($dirbarcode as $dirroot)
				{
					$res=dol_include_once($dirroot.$module.'.php');
					if ($res) break;
				}
				if ($res > 0) $modBarCodeProduct =new $module();
			
				if (!empty($modBarCodeProduct->code_auto)) $product->barcode=$modBarCodeProduct->getNextValue($object,$type);
				
			}
			
			$id = $product->create($user);
			
			if($id>0) {
				if($element == 'propal')$table='propaldet';
				else if($element == 'commande')$table='commandedet';
				
				$db->query("UPDATE ".MAIN_DB_PREFIX.$table." SET fk_product=".$id.",description=''  WHERE rowid=".$lineid);

				if($conf->nomenclature->enabled) { //TODO hook
					$db->query("UPDATE ".MAIN_DB_PREFIX."nomenclature SET fk_object=".$id.", object_type='product' WHERE fk_object=".$lineid." AND object_type='".$element."' ");
				}

			}
			
			echo $id;
			
			break;
		
	}
