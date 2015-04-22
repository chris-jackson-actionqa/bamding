<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Display
 *
 * @author Seth
 */
class Display 
{
  public function beginForm($id, $method, $action)
  {
    ?>
<form id="<?php echo $id; ?>"
      method="<?php echo $method; ?>"
      action="<?php echo $action; ?>">
  
    <?php
  }
  
  public function endForm()
  {
    ?>
</form>
    <?php
  }
  
  public function beginDiv($id="", $class="")
  {
    ?>
<div id="<?php echo $id;?>" name="<?php echo $class; ?>">
<?php
  }
  
  public function endDiv()
  {
    ?>
</div>
  <?php
  }
}
