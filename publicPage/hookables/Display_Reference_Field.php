<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;



class Display_Reference_Field extends Abstract_Action_Hookable

{

    public function __construct()
    {
        parent::__construct('woocommerce_before_add_to_cart_button', 'render_Reference_Input', 10, 1);
    }

    public function render_Reference_Input(): void
    {
        
        ?>

        <p hidden id='project_reference'>
             <label for="project_reference"><?php _e( 'Project Reference:', 'Peleman-Webshop-Package' ); ?> </label>
             <input class="form-control" type="text" id="project_reference" name="project_reference" 
             placeholder="<?php _e( 'Name Your Project', 'Peleman-Webshop-Package' ); ?>" value=""/>
         </p>

     <?php
          
    }

}
