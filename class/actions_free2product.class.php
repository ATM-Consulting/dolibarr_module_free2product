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
	function formObjectOptions($parameters, &$object, &$action, $hookmanager)
	{
		$error = 0; // Error counter
		$myvalue = 'test'; // A result value

		if (in_array('propalcard', explode(':', $parameters['context'])))
		{
		  	
			global $langs;
			$langs->load('free2product@free2product');
			
			if(!empty($object->lines) && empty($action)) {
				
				?><script type="text/javascript">
					function free2product(lineid) {
						
						$a = $('a[lineid='+lineid+']'); 
						var label = $a.attr('label');
						var qty = $a.attr('qty');
						var price = $a.attr('price');
						
						$.ajax({
							url:"<?php echo dol_buildpath('/free2product/script/interface.php',1) ?>"
							,data:{
								put:'change-line'
								,lineid:lineid
								,qty:qty
								,label:label
								,price:price	
								,element:"<?php echo $object->element; ?>"
							}
						}).done(function(fk_product) {
							if(fk_product<=0)alert('ErrorDuringConversion');
							else document.location.href="<?php
								
								if($object->element == 'propal') echo dol_buildpath('/comm/propal.php?id='.$object->id,1);
								else if($object->element == 'commande') echo dol_buildpath('/commande/card.php?id='.$object->id,1);
								
							?>";
							
						});
						
								
					}
				
					$(document).ready(function () {<?php
				
				foreach($object->lines as &$line) {
					
					if($line->product_type <= 1 && $line->fk_product == 0) {
						
						$link='<a href="#" style="float:left;" onclick="free2product('.$line->id.')" lineid="'.$line->id.'" label="'.htmlentities($line->desc).'" qty="'.$line->qty.'" price="'.$line->subprice.'">'.img_left($langs->trans('MakeAsProduct')).'</a>'
						
						?>
						$('tr#row-<?php echo $line->id; ?> td:first').prepend('<?php echo $link; ?>');
						<?php
						
					}
					
				}
				
				?>});</script><?php
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