<?php
require_once(ABSPATH . '/wp-content/tardis/bamding_lib.php');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DisplayCategory
 *
 * @author Seth
 */
class DisplayCategory
{
  public static function show()
  {
    $sMyVenuesURI = Site::getBaseURL() . '/myvenues/';
    $oVenues = new Venues("my_venues", get_user_field('user_login'));
    $categories = $oVenues->getCategories();
    $script_location = Site::getBaseURL() . '/wp-content/js/bookings.js';
    ?>
<script src='<?php echo $script_location; ?>'></script>
<form id="form_category" method="post" action="<?php echo $sMyVenuesURI; ?>">
  <select name="category" onchange="categorySelected(this);">
    <option value="blank" selected></option>
    <option value="add_new">Add New Category</option>
    <?php
    foreach( $categories as $category)
    {
      ?>
    <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
      <?php
    }
    ?>
  </select>
  <input type="text" 
         name="new_category" 
         id="txt_new_category" 
         class="hidden_item"
         onchange="disableAddNewOnEmpty(this);"
         onkeyup="disableAddNewOnEmpty(this);">
  <button type="button" 
          name="btn_add_category" 
          id="btn_add_category" 
          class="hidden_item"
          disabled>
    Add Category
  </button>
  <br />
  <input type="submit" value="Submit" id="btn_category_submit" disabled>
</form>
    <?php
  }
}
