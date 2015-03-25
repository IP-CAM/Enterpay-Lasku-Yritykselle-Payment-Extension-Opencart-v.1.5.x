<?php echo $header; ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="box">
  <div class="left"></div>
  <div class="right"></div>
  <div class="heading">
    <h1 style="background-image: url('view/image/laskuyritykselle.png');"><?php echo $heading_title; ?></h1>
    <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
  </div>
  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <table class="form">
        <tr>
          <td><span class="required">*</span> <?php echo $entry_title; ?></td>
          <td><input type="text" name="enterpay_title" value="<?php echo $enterpay_title; ?>" />
            <?php if ($error_title) { ?>
            <span class="error"><?php echo $error_title; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_merchant; ?></td>
          <td><input type="text" name="enterpay_merchant" value="<?php echo $enterpay_merchant; ?>" />
            <?php if ($error_merchant) { ?>
            <span class="error"><?php echo $error_merchant; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_sellerkey; ?></td>
          <td><input type="text" name="enterpay_sellerkey" value="<?php echo $enterpay_sellerkey; ?>" />
            <?php if ($error_sellerkey) { ?>
            <span class="error"><?php echo $error_sellerkey; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_sellerkeyver; ?></td>
          <td><input type="text" name="enterpay_sellerkeyver" value="<?php echo $enterpay_sellerkeyver; ?>" />
            <?php if ($error_sellerkeyver) { ?>
            <span class="error"><?php echo $error_sellerkeyver; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_enterpayversion; ?></td>
          <td><input type="text" name="enterpay_enterpayversion" value="<?php echo $enterpay_enterpayversion; ?>" />
            <?php if ($error_enterpayversion) { ?>
            <span class="error"><?php echo $error_enterpayversion; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><?php echo $entry_order_status; ?></td>
          <td><select name="enterpay_order_status_id">
              <?php foreach ($order_statuses as $order_status) { ?>
              <?php if ($order_status['order_status_id'] == $enterpay_order_status_id) { ?>
              <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td>
        </tr>
          <td><?php echo $entry_order_status_pending; ?></td>
          <td><select name="enterpay_order_status_pending_id">
              <?php foreach ($order_statuses as $order_status) { ?>
              <?php if ($order_status['order_status_pending_id'] == $enterpay_order_status_id) { ?>
              <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_geo_zone; ?></td>
          <td><select name="enterpay_geo_zone_id">
              <option value="0"><?php echo $text_all_zones; ?></option>
              <?php foreach ($geo_zones as $geo_zone) { ?>
              <?php if ($geo_zone['geo_zone_id'] == $enterpay_geo_zone_id) { ?>
              <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_status; ?></td>
          <td><select name="enterpay_status">
              <?php if ($enterpay_status) { ?>
              <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
              <option value="0"><?php echo $text_disabled; ?></option>
              <?php } else { ?>
              <option value="1"><?php echo $text_enabled; ?></option>
              <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_reference; ?></td>
          <td><input type="text" name="enterpay_reference" value="<?php echo $enterpay_reference; ?>" size="1" /></td>
        </tr> 
         <tr>
          <td><?php echo $entry_test; ?></td>
          <td><select <input type="text" name="enterpay_test"/>
             <option value="1" <?php echo $enterpay_test == 1?'selected="selected"':''?>><?php echo $text_yes; ?></option>
             <option value="0" <?php echo $enterpay_test == 0?'selected="selected"':''?>><?php echo $text_no; ?></option>
             </select>
        </tr>
            <tr>
              <td><?php echo $entry_sort_order; ?></td>
              <td><input type="text" name="enterpay_sort_order" value="<?php echo $enterpay_sort_order; ?>" size="1" /></td>
            </tr>

      </table>
    </form>
  </div>
</div>
<?php echo $footer; ?>
