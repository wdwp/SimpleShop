<ul>
    <li>Version 1.0 December 2022</li>
	<ul>
	<li>Rewritten for cmsms 2.x</li>
	</ul>
	<li>Version 0.3.9 4 December 2012<br />
		<ul>
			<li>Link to SimpleCart updated so pretty URL's can be used;</li>
			<li>Last used category is remembered in back end;</li>
			<li>Image description of first image now available in Category & Products template as $entry->imagedesc;</li>
			<li>Problem with long-named product images (bug# 7315. Solution provided by <a href"=http://dev.cmsmadesimple.org/users/Gast00n" target="_blank">Gast00n</a>)</li>
			<li>Minor changes in default stylesheet for the shop;</li>
			<li>Version can only be used as of CMSMS 1.10;</li>
			<li>Removed junk parameter from pretty url towards product detail;</li>
			<li>Added extra example in category/productlist template for multiple columns with products;</li>
			<li>Included link in help text to the translations;</li>
			<li>No return after editing a product (bug# 7314). New button 'Save and Return' solves this;</li>
			<li>Images folder not created correctly (bug# 7313. Solution provided by <a href"=http://dev.cmsmadesimple.org/users/Gast00n" target="_blank">Gast00n</a>).</li>
		</ul>
	</li>
	<li>Version 0.3.8 1 December 2011<br />
		<ul>
			<li>Added parameters to overcome wrongly passed on front end;</li>
			<li>On the category list, new fields are made available: entry->catname and entry->catcount;</li>
			<li>Typo caused products_in_root_category never to be shown correctly when using display=catlist;</li>
			<li>Resolved error when search for product was used (bug# 7210);</li>
			<li>Make sure no logging to admin log is done by setting restricted parameters.</li>
		</ul>
	</li>
	<li>Version 0.3.7 22 October 2011<br />
		<ul>
			<li>Price and weight were not saved correctly due to number formating.</li>
		</ul>
	</li>
	<li>Version 0.3.6 12 October 2011<br />
		<ul>
			<li>All checks, if search module was installed, are removed;</li>
			<li>Item number is now shown on list of items;</li>
			<li>Search by item number now available;</li>
			<li>When adding/changing a product and Inventory Management is installed, product extension info can be entered on same page;</li>
			<li>New function in back end added: quick selector for easy locating a category;</li>
			<li>Price formatting follows rules of CartMS (if not installed see options) (feature request:#6657);<br />
			Weight formatting is now controlled by number formatting in the options.<br />
			You will have to remove all '|number_format:2' references in all ShopMS templates.</li>
			<li>Only active products are shown in the featured list of products;</li>
			<li>Pagination build in for list of featured products (feature request:#6710);</li>
			<li>Improved flow after pressing cancel in product edit (correct category path shown now);</li>
			<li>Version prepared for use with CMSMS vs 1.9.4.x and 1.10.</li>
		</ul>
	</li>
	<li>Version 0.3.5 2 August 2011<br />
		<ul>
			<li>Add to cart from product list was able to add second line with same product;</li>
			<li>Minor changes so module works with Postgress database as well (thanks go to Brett of Binarystudios.net).</li>
		</ul>
	</li>
	<li>Version 0.3.4 12 June 2011<br />
		<ul>
			<li>Time of decreasing the inventory stock level can now be set via preferences;</li>
			<li>Pagination didn't work correctly on product list page (after selecting category);</li>
			<li>Attribute item numbers were not capitalized automatically when option to do so was on;</li>
			<li>Pagination didn't work when only one item should be shown per page (bug# 3887);</li>
			<li>Calculation of attribute price was wrong when price adjustment was '+' or '-';</li>
			<li>New variable available in the the product listing template: availablestock. This holds the 
			quantity available of the product that is on stock;</li>
			<li>If inventory management module is in use, sales transactions are passed;</li>
			<li>Changed the maximum version of CMSMS to make sure no problems with version 1.10 of CMSMS.</li>
		</ul>
	</li>
	<li>Version 0.3.3 26 May 2011<br />
		<ul>
			<li>Extended lenght of name of product input to 255 in total;</li>
			<li>If inventory type preference set to 'Not used', allow add to cart option.</li>
		</ul>
	</li>
	<li>Version 0.3.2 21 December 2010<br />
		<ul>
			<li>Attribute description was not saved when attribute was added;</li>
			<li>When editing a category, the category that is edited can't be set as the parent category;</li>
			<li>Dependent on inventory settings, labels of fields on product and attribute level are shown or hidden;</li>
			<li>If product is deactivated, its search index entries are removed.</li>
		</ul>
	</li>
	<li>Version 0.3.1 17 November 2010<br />
		<ul>
			<li>When uninstalling, the search index entries of the products are removed;</li>
			<li>Preferences now holds field that allows tracking of inventory on product or attribute level (Request# 4501);</li>
			<li>Make max attributes on product level maintainable;</li>
			<li>Not active attributes where shown on front end, from this moment on this is not the case anymore.</li>
		</ul>
	</li>
	<li>Version 0.3.0 6 November 2010<br />
		<ul>
			<li>Name of shop in preferences is now used in document properties of invoice generated in Cart Made Simple;</li>
			<li>Big change on the permission system. Note --> All previous Shop Made Simple permissions are gone when upgraded! Sorry, this could not be avoided;</li>
			<li>Showing multiple levels in categories now posible with parm catlist (see help text);</li>
			<li>Include price formating in default category/products template;</li>
			<li>Get rid of public member variables such as 'global $gCms';</li>
			<li>Minimum version set to 1.8.1 due to removal public member variables;</li>
			<li>Removed call to sometimes not working exif_image;</li>
			<li>Made better use of path directory separator;</li>
			<li>Introduction of maximum number of attributes per product;</li>
			<li>New preferences: 'maximum attributes' and 'allow duplicate item number' added to preference page;</li>
			<li>Since it had totaly no function, default language has been removed from preferences page;</li>
			<li>Changed default product detail template with regards to number of decimals on price;</li>
			<li>Table attribute_options removed, content was not maintainable, all fields now in table product_attributes;</li>
			<li>Attributes of product now hold price factor and various other fields (Request# 3826);</li>
			<li>Moved functions internally around, so module becomes a bit more like standard;</li>
			<li>Version 0.2.9 on purpose skipped, since this is a major release. Since this version breaks with the original, Duketown thanks 
			Gary Greyling (Lahuindai) for all his perfect work. Time to move on.</li>
		</ul>
	</li>
	<li>Version 0.2.8 9 September 2010<br />
		<ul>
			<li>No more problems with product prices greater than 999 (bug# 4511);</li>
			<li>Allow module to run in 1.8 environment of CMSMS;</li>
			<li>Rename of class Products to SMSProducts so module can run next to products module;</li>
			<li>Name of product has been increased with number of characters from 30 to 255;</li>
			<li>Work done on pretty URL (bug# 3122&4081) solved by drumzone;</li>
			<li>Netweight now available as variable in the product detail overview.</li>
		</ul>
	</li>
	<li>Version 0.2.7 1 March 2010<br>
		<ul>
			<li>Input parameters were not cleaned at correct moment causing warnings;</li>
			<li>Category and products template that is used during installation extended to include how to use image of add to cart;</li>
		</ul>
	</li>
	<li>Version 0.2.6 1 November 2009<br>
		<ul>
			<li>Set maximum CMSMS version to 1.6.9 to overcome problems;</li>
			<li>Change log not in translation anymore, so this log is only available in English;</li>
			<li>Prepare add to cart link with image (Feat Req#3324). Image taken is SimpleShop/images/addtocart.gif. Change this image if needed.<br>
			Use {$addproductimage} in the template in stead of {$addproduct}, which is the text;</li>
			<li>Removed product id on default featured products template.</li>
		</ul>
	</li>
	<li>Version 0.2.5 10 August 2009<br>
		<ul>
			<li>Missing code in front end product detail. (bug# 3850) solved by NaN.</li>
		</ul>
	</li>
	<li>Version 0.2.4 26 July 2009<br>
		<ul>
			<li>Automatic installation of stylesheet \'Module: Shop Made Simple\' was not working (stylesheet will be inserted during upgrade from version 0.2.3 to 0.2.4);</li>
			<li>Activating and deactivating was not working correctly for categories in list of categories, change also applied for products;</li>
			<li>No test data created anymore automatically during installation;</li>
			<li>On product detail new variables introduced: {imagethumb} and {fullpathimage} to support images better;</li>
			<li>No category and product id used anymore in installed template of catagory list;</li>
			<li>Default currency is set to Euro;</li>
			<li>Work done on pretty URL --> not fully functional;</li>
			<li>Bug solved (by Fatlizard) to handle correct product_id in fe product detail.</li>
		</ul>
	</li>
	<li>Version 0.2.3 17 May 2009<br>
		<ul>
			<li>Initialized variables correctly, so no warning appears for them when php.ini states to show warnings;</li>
			<li>New preferences for thumbnail generation. Use the rebuild button to generate thumbnails from earlier loaded images.</li>
			<li>The detail template has been changed to allow switching images. A simpler version is available to copy from in the
			templates directory. The name is fe_product_detail1.tpl.<br>
			If you have upgraded from an earlier version you may want to use the switching of images. Look in templates\fe_product_detail.tpl.</li>
			<li>Original template of products per category changed to show image with link on product line.</li>
		</ul>
	</li>
	<li>Version 0.2.2 8 Mar 2009<br>
		<ul>
			<li>Show name without link to detail on product listing (feature request# 2986): use prodnamenolink in stead of prodname in the template.</li>
			<li>Build in link from image to detail on product listing (feature request# 3083): use prodimagelink in stead of prodimage in the template.</li>
		</ul>
	</li>
	<li>Version 0.2.1 15 Jan 2009<br>
		<ul>
			<li>Typo caused display=productlist catname=... not to work correctly. Bug solved.</li>
		</ul>
	</li>
	<li>Version 0.2.0 11 Jan 2009<br>
		<ul>
			<li>Currency symbol available for category list (use $entry->cursymbol};</li>
			<li>Templates can now be adjusted to decrease length of description (was hardcoded) using |truncate in template;</li>
			<li>Hardcoded signaling that there are no categories or products is removed, now part of translation;</li>
			<li>If the module Search has been installed, the name and description of products can be searched upon;</li>
			<li>Bug solved on updating category information. Editor was not working well;</li>
			<li>Description of category and product could span two lines. Now always only one line;</li>
			<li>New preference added to hold the number of products per page;</li>
			<li>Using parameter prodpagelimit, the number of products per page can be influenced (overriding the preference).<br>
			Paging is possible using the following in the Categories/Products template:<br>
			{if $pagecount > 1}<br>&nbsp;{$firstpage} {$prevpage} {$pagetext}:  {$pagenumber} {$oftext}: {$pagecount} {$nextpage} {$lastpage}<br>{/if}
			<br></li>
			<li>Internal clean up of front end category listing program.</li>
		</ul>
	</li>
	<li>Version 0.1.9 29 Dec 2008<br>
		<ul>
			<li>Removed translation bug during installation.</li>
		</ul>
	</li>
	<li>Version 0.1.8 11 Dec 2008<br>
		<ul>
			<li>Solved issue where switch status on product caused description to be cleared;</li>
			<li>Edit of Stock Keeping Unit was not working;</li>
		</ul>
	</li>
	<li>Version 0.1.7 9 Dec 2008<br>
		<ul>
			<li>Add to cart available at list of products on front end;</li>
			<li>New fields: Stock Keeping Unit (SKU) and itemnumber available per product;</li>
			<li>Maintenance on SKU is possible;</li>
			<li>Additional preference to allow only capital characters in itemnumber;</li>
			<li>Additional preference to set default SKU during add of a product.</li>
		</ul>
	</li>
	<li>Version 0.1.6 12 Nov 2008<br>
		<ul>
			<li>Image of category is now displayed in front of category name in category listing;</li>
			<li>Length of product and attribute description enlarged;</li>
			<li>Check build in during installation if the conflicting module Products has been installed;</li>
			<li>Bug on adding new products corrected (was always pointing to root after adding);</li>
			<li>Deletion of product returns to last used category;</li>
			<li>After Switch of status, last shown category/products list is shown;</li>
			<li>Description boxes for product, attributes and images improvements;</li>
			<li>Prepare correct connection of multiple categories to one product;</li>
			<li>Variable $image in detail template renamed to $prodimage, since it might be in use in other modules (like printing);</li>
			<li>When last category connection to product is deleted, the product will be connected to the root (otherwise it can\'t be selected anymore);</li>
			<li>Multiple levels of categories is allowed as of version 0.1.5 but is not supported on front end! (sorry).</li>
		</ul>
	</li>
	<li>Version 0.1.5 June 2008<br>
		<ul>
			<li>Multiple levels of categories are allowed/possible;</li>
			<li>New parameter for frontend: parentcategory.</li>
		</ul>
	</li>
	<li>Version 0.1.4 May 2008<br>
		<ul>
			<li>Upload of image for category has been changed;</li>
			<li>Changed install so directory path correct for categories and products.</li>
		</ul>
	</li>
	<li>Version 0.1.3 May 2008<br>
		<ul>
			<li>All front end pages are handled by templates derived from admin part;</li>
			<li>Administrator now has tab containing templates as used on front end;</li>
			<li>Front end shows wether prices are incl. or excl. VAT dependent on preference;</li>
			<li>Products connected to category sorted by list order;</li>
			<li>Overview categories in back end and front end now sorted by list order.</li>
		</ul>
	</li>
	<li>Version 0.1.2 - May 1st 2008<br>
		<ul>
			<li>When a category is deleted, its connected products will be connected to the root category.</li>
			<li>Some buttons lacked proper translations.</li>
		</ul>
	</li>
	<li>Version 0.1.1 - April 2008<br>
		<ul>
			<li>Now option to show products of one category in single pages using parameters.</li>
			<li>Some fields turned into float to correctly handle decimals.</li>
			<li>Dropped admin preferences to hold shop policies (use content block or normal CMSMS page with the policies).</li>
			<li>Introduction of checkmark if prices are inclusive or exlcusive VAT in preferences.</li>
			<li>Also in preferences now unit of measure for weights of the products.</li>
			<li>Introduction of VAT code and nett weight per product. For calculation of (shipping) cost in Cart module</li>  
		</ul>
	</li>
	<li>Version 0.1.0 - April 18th 2008 Roll out version<br>
		<ul>
			<li>Layout changes made</li>
		</ul>
	</li>
	<li>Version 0.0.4 - Import and Export Release<br>
		<ul>
			<li>Categories</li>
			<li>Products</li>
		</ul>
	</li>
	<li>Version 0.0.3 - Client Interface Release<br>
		<ul>
			<li>Client interface tags</li>
		</ul>
	</li>
	<li>Version 0.0.2 - Administration Extended Release
		<ul>
			<li>Templates, Events, Orders, Translations</li>
		</ul>
	</li>
	<li>Version 0.0.1 - Administration Release<br>
		<ul>
			<li>Preferences - Completed on 24/12/2007</li>
			<li>Categories - Completed on 25/12/2007</li>
			<li>Products</li>
		</ul>
	</li>
</ul>