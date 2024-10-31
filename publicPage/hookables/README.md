# Public Page

All the PWP plugin public hookables are to be found here. Each hookable represents a single hookable method, be it a filter, action, ajax call, cron job, or shortcode.

## PWP_Add_Custom_Project_On_Return
One of the main functionalities of the plugin is the ability to redirect a user to an external image editor to customize a product before adding a product to the cart. For this to work, a custom project's data has to be stored in a local session variable while the user is redirected to the external editor. This hookable is called to add the project to the cart when the user has finalized their project and is redirected to the webshop.

## PWP_Add_Fields_To_Add_To_Cart_Button
Adds additional fields of hidden input to the add to cart button in the store

## PWP_Add_Item_To_Cart
OBSOLETE

## PWP_Add_Metadata_To_Order_Line_Item
copies metadata from a cart item to an order line item.

## PWP_Add_PDF_Data_To_Cart_Item
Hookable filter to add PDF data from the uploaded files global (`$_FILES`) to the cart item. Allows for customers/users to upload PDF files to a product they wish to order.

## PWP_Add_PDF_Prices_To_Cart
Adds additional price per page * pages for a product with a pdf upload to the product's total price in the cart.

## PWP_Ajax_Add_To_Cart
Ajax hookable that enables the special redirect functionality of the plugin. If a product is not customizable, it will default to the default add to cart functionality. Otherwise, it will store the item data locally, and redirect to an external editor.

## PWP_Ajax_Show_Variation
Helps display variation specific data in the woocommerce webshop through ajax calls.

## PWP_Ajax_Upload_PDF
OBSOLETE
old code for uploading and validating PDF files through Ajax calls.

## PWP_Change_Add_To_Cart_Archive_Button
filter for changing the add to cart button text in the archive to something else

## PWP_Change_Add_To_Cart_Button_Label
filter for changing the add to cart button text on a product's shop page

## PWP_Change_Add_To_Cart_Label_For_Archive
OBSOLETE

## PWP_Delete_Project_On_Order_Cancelled
helper action. if an order is cancelled, will delete the PDF/project entries and related files from the server. Helps keep the system clean

## PWP_Display_Editor_Project_Button_In_Cart
adds a button to cart item lines to redirect the customer to the project editor, allowing them to make changes before ordering.

## PWP_Display_PDF_Data_After_Order_Item
adds a download button and pdf information to order line items with PDF uploads

## PWP_Display_PDF_Data_In_Cart
displays pdf information on individual order line items with PDF uploads

## PWP_Display_PDF_Fields_On_Variations
displays the PDF requirement fields on variations in the webshop

## PWP_Display_PDF_Upload_Form
displays the PDF upload form on products in the webshop

## PWP_Enqueue_Public_Styles
enqueues CSS styles for the public side of the webshop

## PWP_Order_Project
hook called when an order is made. will set any projects listed in the Cart to ordered.

## PWP_Overide_WC_Templates
OBSOLETE

## PWP_Remove_PDF_on_Cart_Delection
removes pdfs and projects when the related item is removed from the cart.

## PWP_Save_Cart_Item_Meta_To_Order_Item_Meta
takes metadata saved on the cart item and adds it to the order item upon ordering. makes meta data easier to access after the fact.

## PWP_Validate_PDF_Upload
Before an item is added to the cart, if it requires a PDF upload, this filter will validate the uploaded PDF file for type, format, and page count.
