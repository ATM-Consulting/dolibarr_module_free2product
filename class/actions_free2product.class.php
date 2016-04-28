<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2015 ATM Consulting <support@atm-consulting.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    class/actions_free2product.class.php
 * \ingroup free2product
 * \brief   This file is an example hook overload class file
 *          Put some comments here
 */

/**
 * Class ActionsFree2Product
 */
class ActionsFree2Product
{
	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var array Errors
	 */
	public $errors = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
	}

	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	
	function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager){
		
		if (
			in_array('propalcard', explode(':', $parameters['context'])) 
			|| in_array('ordercard', explode(':', $parameters['context']))
		)
		{
			global $langs;
			$langs->load('free2product@free2product');
			
			global $addButtonToConvertAll;
			
			if($addButtonToConvertAll) {
				
				?>
				<div class="inline-block divButAction"><a class="butAction" href="javascript:convertAllFreeLine();""><?php echo $langs->trans('convertAllFreeLine') ?></a></div>
				<?php
				?>
					<div id="convertAllFreeLine_popup" style="display:none">
						<?php 
						$formCore=new TFormCore;
						?>
						<table class="border" width="100%">
							<?php
								
								foreach($object->lines as &$line) {
					
									if($line->product_type <= 1 && $line->fk_product == 0) { 
									
										echo '<tr>
											<td>'.$formCore->texte('', 'TFreeProduct['.$line->id.'][ref]', 'FREELINE-'.$line->id, 15,255,' lineid="'.$line->id.'" label="'.htmlentities(addslashes($line->desc)).'" qty="'.$line->qty.'" price="'.$line->subprice.'" product_type="'.$line->product_type.'" ').'</td>
											<td>'.$line->desc.'</td>
											<td align="right">'.price($line->subprice).'</td>
											<td align="right">'.price($line->qty).'</td>
										</tr>';
										
									
									}							
								
								}
							?>
						</table>
						
					</div>
					<?php
			}
			
		
		}
		
	} 
	
	function formObjectOptions($parameters, &$object, &$action, $hookmanager)
	{
		$error = 0; // Error counter
		$myvalue = ''; // A result value

		if (
			in_array('propalcard', explode(':', $parameters['context'])) 
			|| in_array('ordercard', explode(':', $parameters['context']))
		)
		{
		  	
			global $langs;
			$langs->load('free2product@free2product');
			
			if(!empty($object->lines)) {
				
				?><script type="text/javascript">
					function convertAllFreeLine() {
						$('#convertAllFreeLine_popup').dialog({
							title:"<?php echo $langs->transnoentities('convertAllFreeLine') ?>"
							,modal:true
							,width:'80%'
							,buttons: {
					        	"Convertir ces lignes": function() {
					        		$('#convertAllFreeLine_popup input[lineid]').each(function(i,item) {
					        			
					        			$item = $(item); 
					        			
					        			var ref = $item.val();
										var lineid = $item.attr('lineid');
										var label = $item.attr('label');
										var qty = $item.attr('qty');
										var price = $item.attr('price');
										var product_type = $item.attr('product_type');
										
					        			convert_free2product(lineid,ref,label,qty,price,product_type);
					        		});
					        		
					        		
					        		
					        		document.location.href="<?php
												if($object->element == 'propal') echo dol_buildpath('/comm/propal.php?id='.$object->id,1);
												else if($object->element == 'commande') echo dol_buildpath('/commande/card.php?id='.$object->id,1);
									?>";
					          		$( this ).dialog( "close" );
					        	}
							}
						});
					}
						
					function convert_free2product(lineid,ref,label,qty,price,product_type) {
						
						if(ref) {
							$.ajax({
								url:"<?php echo dol_buildpath('/free2product/script/interface.php',1) ?>"
								,data:{
									put:'change-line'
									,lineid:lineid
									,qty:qty
									,label:label
									,price:price
									,ref:ref	
									,product_type:product_type
									,element:"<?php echo $object->element; ?>"
								}
								,async:false
							}).done(function(fk_product) {
								if(fk_product<=0)alert('ErrorDuringConversion '+ref);
								
							});
						}
						
					}
						
					function free2product(lineid) {
						
						$a = $('a[lineid='+lineid+']'); 
						var label = $a.attr('label');
						var qty = $a.attr('qty');
						var price = $a.attr('price');
						var product_type = $a.attr('product_type');
						
						var ref = window.prompt("<?php echo $langs->transnoentities('ConvertToNewProductRef') ?>","FREELINE-"+lineid);
						convert_free2product(lineid,ref,label,qty,price,product_type);
						
						
						
								
					}
				
					$(document).ready(function () {<?php
				
				global $addButtonToConvertAll;
				$addButtonToConvertAll = false;
				
				foreach($object->lines as &$line) {
					

					if($line->product_type <= 1 && $line->fk_product == 0) { // Ceci est une ligne libre
						$addButtonToConvertAll=true;
					
						$link='<a href="javascript:;" style="float:left;" onclick="free2product('.$line->id.')" lineid="'.$line->id.'" label="'.htmlentities(addslashes(strtr($line->desc,array("\n"=>'\n',"\r"=>'')))).'" qty="'.$line->qty.'" price="'.$line->subprice.'" product_type="'.$line->product_type.'">'.img_left($langs->trans('MakeAsProduct')).'</a>'
						
						?>
						$('tr#row-<?php echo $line->id; ?> td:first').prepend('<?php echo $link; ?>');
						<?php
						
					}
					
				}
				
				?>
				});
				</script>
				<?php
				
				
			}
			
			
			
		}

		if (! $error)
		{
			return 0; // or return 1 to replace standard code
		}
		else
		{
			$this->errors[] = $error_msg;
			return -1;
		}
	}
}
