<form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" id="payment">
<?php
 if($enterpay_debug) { echo 'Payment module in test mode. Request displayed.<br /><br><div style="border: 1px solid #aaaaaa; font-family: courier; font-size: xsmall;">'; }
   foreach($enterpay_params as $key => $value) {
    echo "<input type=\"hidden\" name=\"{$key}\" value=\"".htmlentities($value, ENT_COMPAT, "UTF-8")."\" />\n";

    if($enterpay_debug) {
      echo "{$key} = {$value}<br/>\n";
    }
  }
  echo "<input type=\"hidden\" name=\"hmac\" value=\"". $enterpay_hmac."\" />";

if($enterpay_debug) {
      echo "</div>\n";
    }
?>
</form>
<div class="buttons">
  <div class="right">
    <a onclick="$('#payment').submit();" class="button"><span><?php echo $button_confirm; ?></span></a>
  </div>
</div>

