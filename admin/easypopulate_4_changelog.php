<?php
// 1.3.00 - 02-19-09 - Performed much code cleaning.
// 1.3.01 - 03-30-09 - BUG FIX - when uploading FULL file. If v_status column is left out, would re-activate deactivated items.
// 1.3.02 - 03-30-09 - BUG FIX - fixed blank manufactuers import where empty v_manufacturers_name when blank was not reset correctly
// 1.3.03 - 09-08-09 - Begins Major Code Revisions
/* 1.3.03 Notes:
- Simplified ?langer=install and ?langer=remove  "langer" to "epinstaller", removed ?langer=installnew
- removed paypal request for donations!!
- no longer creates first export file
- changed logic of function ep_chmod_check() ... it now ONLY checks to see if the directory exist and is writeable and fails if either is not true.
  Also removed automatic attemps to chmod() throught this script - this is not secure.
09-15-09 - Decided to also split this mod off properly by adding TAB (like CSV and OSC)
09-29-09 - Created multiple exports for product attributes, option names, and option values
09-29-09 - updates Price/Qty/Breaks export code to use while statement. Now allow for unlimited in number of price breaks! 
10-01-09 - phazie's array definitions for filelayouts are more efficient and also more code readible and mod-able. array_merger() if very inefficient
			this required some tweaking in other places
		 - found some places in the download code that I replace with exclusion code to speed up downloads. No sense executing code that wasn't needed.
		 - export time in now in 24-hour:minute:second which makes better sense to me
10-02-09 - added metatags for products as in phazie's csv version - so far testing is good
10-05-09 - it is VERY important to have the correct delimiter set or the import of information will fail
*/
// 1.3.04 - 10-13-09 - Fixed function call errors and added filtering check for UPC and UOM mods when downloading files
// 1.3.05 - 10-14-09 - Auto switching for file extention fixed dependant on delimitor <tab> or <comma>
// 					 - Somehow when adding a new item with empty manufacturer, it was being assigned the previously imported manufactures id. Spelling error in code!

// 4.0.000 - 12-21-09 - Begin Move to 4.0.000 beta
// 4.0.001 - 12-22-09 - Ha! Manufacturers weren't being created because I left off the "s" in manufacturers!
// 4.0.02  - 06-03-10 - changed all price_as to price_uom for unit of measure. This is a logical correction. Databased updated
// ALTER TABLE products ADD products_upc VARCHAR(32) AFTER products_model;
// ALTER TABLE products ADD products_price_uom VARCHAR(2) AFTER products_price;
// 4.0.03 - 11-19-2010 Fixed bug where v_date_avail (products_date_available) wasn't being set to NULL correctly.
// 4.0.04 - 11-22-2010 worked on quantity discount import code. now removes old discounts when discount type or number of discounts changes
// 4.0.05 - 11-23-2010 more work on quantity breaks. Eliminated the v_discount_id column since all discounts are entered at once fresh i'm just using loop index
//          11-23-2010 added products_status to the Model/Price/Qty and Model/Price/Breaks
// 4.0.06 - 12-02-2010 added uninstall button on form page, removed unnecessary tables from form page
//			12-02-2010 removed 'v_discount_id_' from export file layout - no longer needed
//			12-02-2010 add EASYPOPULATE_4_CONFIG_MAX_QTY_DISCOUNTS variable to configuration page
// 4.0.07 - 12-02-2010 adding categories image, description and metadata export/import
//			12-03-2010 completing Categories image/description/metadata export/import feature!
// 4.0.08 - 12-07-2010 found error: when uploading Model/Price/Qty to set Specials Pricing, quantity discounts are being wiped
// 4.0.09 - 12-10-2010 time to start breaking this down and using more functional design (and hopefully lead toward an OO based design)
//			12-10-2010 move all file layout to functions now using $filelayout = ep_4_set_filelayout();
// 			12-11-2010 broke out all export code into easypopulate_4_export.php - This is included with a require_once() statement.
//                     I am doing this so I can start to break this mod into more functional sections.
//			12-13-2010 broke out all import code into easypopulate_4_import.php
// 			12-13-2010 moved these notes to easypopulate_4_changelog.php
//			12-13-2010 changing the way categories are imported and exported. now using single column for categories where categories are separated
//						by a delimeter. Should allow "unlimited" category depth, but still a practical limit of 256 character for excel although
//						OpenOffice is not restricted. Multi-lingual categories should be achievable now.
//			12-14-2010 beginning work on category import using single delimeted column 
//			12-15-2010 export of mult-lingual categories is now working. Export/Import of "categorymeta" works correctly to assing multi-lingual values
//                     Have not tested "full" import with multi-lingual categories. Must look closely at code. I may have to make the "full" import
//                     only work with default language, and use 'categorymeta' for updating other languages. 
//                     Why? Because error checking on import may become to onerous.
//			12-16-2010 fixed small error in configuration keys where key was misspelled
//					   removed all references to now defunct $max_categories
// 4.0.10	12-16-2010 Simple single-line attribute import code merged into EP4. Initial testing is good.

?>