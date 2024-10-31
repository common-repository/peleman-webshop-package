# Peleman-Webshop-Package
successor to the Peleman Product Integrator

The Peleman Webshop Package plugin extends Wordpress and Woocommerce to support the sale and customization of Peleman products in an online webshop. It integrates PDF uploads for book contents and a connection to the external PIE editor for the customization of covers, cards, photobooks, and other customizable Peleman products.

## installation and setup
### installation
1) in the wordpress admin control panel, go to plugins >> Add new
2) at the top of the page, press the `upload plugin` button
3) drag & drop the plugin .zip file, or select the file.
4) press `install now`
5) if an older version of the plugin was already installed, you will see a dialogue comparing the old version with the newly uploaded version. Click  `replace current with uploaded`
6) When installation is complete, click `activate plugin`

the plugin is now successfully installed and activated.
### configuration
with the plugin installed and activated, the admin control panel now has an extra option titled `Peleman PWP`. Through this you can access two menus for configuration.
#### Editor Settings
in this menu you can configure the connection from the Plugin to the PIE editor for product customization.
* **PIE Domain (url)** - base url of the PIE webshop the plugin will connect to.
* **PIE Customer ID** - enter your customer ID here. The ID will connect any projects created by customers of your webshop to your account on the editor.
* **PIE API Key** - your API key for the PIE editor.

the Imaxel fields can be ignored, since they are no longer used or supported by the plugin.

#### Peleman PWP
the main configuration menu of the plugin, it nonetheless holds the less critical configuration options

In this menu you can customize some of the labels in the webshop, specifically Archive labels:
* **Simple product - customizable** - which label to display in the product archive/overview page for simple customizable products. Customizable products are products which require either PIE editor customization, or a PDF file.
* **Variable product - customizable** - which label to display in the product arhive/overview page for variable custmoizable products.

the final option is the more important one: **PDF cleanup cutoff date**

When a customer wants to order a product which requires a PDF, the PDF file they upload will be saved on the system server until the order is made. This could lead to a great deal of clutter and waste files, as some orders will never be completed. As such, this variable defines an amount of days after which an uploaded PDF gets automatically deleted.

the default cutoff count is **15 days**. It is recommended to never make this amount any less than the expiration time of the customer's shopping cart. It is better to hold onto a file too long than deleting it too fast.

    Note: PDF files which are ordered are never automatically deleted by the plugin.

### product definitions
The PWP plugin adds many new parameters to the standard WooCommerce product pages for the correct integration of Peleman Products. These variables are essentially the same for simple products and variant products. For more information about different product types, please see the WooCommerce documentation.

under **General**/**Variations**, there are the following PWP specific fields:
* **Custom Add To Cart Label** - custom add to cart label for the product. If left empty, the label will be the default for the webshop. In variable products, this label can be set for all variations here, and each variant has the option to have a custom label for itself, allowing for a great amount of flexibility in picking and customizing Add to Cart labels.
* **Unit Purchase Price** - purchase price of a singular unit of this item. Only useful if this item is not sold as an individual item, but as a batch of units (ie. a box of 20 units)
* **Unit amount** - amount of item units in an item (an item being 1 box, containing 20 units). This has no functional purpose within the plugin, only serves to clarify the product to the customer.
* **editor** - which editor to use for the customization of Peleman Products. By default, the plugin only supports the use of the PIE editor.
* **use project preview thumbnail in cart** - If checked, the webshop will attempt to retrieve a thumbnail of the product from the image editor and replace the default item preview in the cart.
* **PIE template ID** `required`- which PIE template ID will be used for this product in the PIE editor.
* **Design ID** - which design ID will be used for this product in the PIE editor.
* **instructions** - a series of PIE editor instructions, separated by spaces. Check the PIE editor documentation for a list of accepted instructions.
* **Color code** - color code for use in the PIE editor. Will color the background of the editor to this color, simulating colored paper or a cover. This color will not be present in the final print file, but will be present in any previews of the finished project.
* **PIE background ID** - Same as the Color code, will put a background image in the editor which will not be present in the final print file. Used for pre-designed covers, or textured surfaces.
* **use image uploads** - if checked, when a customer orders a customizable product they will be redirected to an image upload page (uppy) before being directed to the editor itself. This allows them to upload their own images beforehand, which is necessary for the photobook autofill functionality of the editor. This option should be checked if autofill functionality is desired.
*  **autofill template pages in editor** - enables autofill functionality in the editor. 
*  **format id**
*  **pages to fill** - amount of pages for the autofill method to fill. Does not need to match the amount of images the user can upload.
*  **Min images for upload** - minimum amount of images the customer has to upload before being able to access the Editor. If left at 0, the customer does not need to upload any images.
*  **Max images for upload** - maximum amount of images the customer can upload to uppy. If left at 0, there is no maximum. If a customer has to upload a specific amount of images, no more or less, simply set this to be the same as the `min images for upload` value.
*  **require PDF upload** - check this if the product requires a PDF upload. this is useful for products such as the thesis, where a customer can create their own book.
*  **pdf min pages** - minimum amount of pages the PDF must contain. by default should be 1.
*  **pdf max pages** - maximum amount of pages the PDF must contain. Leave at 0 for no maximum. If the PDF has to have a set amount of pages, set this to be the same as `PDF min pages`.
*  **pdf format width**- maximum width of the pdf file for print, in mm. If left at 0, there is no restriction on the width.
*  **pdf format height** - maximum height of the pdf file for print, in mm. If left at 0, there is no restriction on the height.
*  **pdf price per page** - how much each pdf page will cost, added to the default price of the product.

### user notes
* the project is set up to use PHPDocumentor 3 for auto documentation
* rename the .phar file to `phpDoc.phar`
* in order to run PHPDocumentor, use the command `php phpDoc.phar -d . -t docs/api`
