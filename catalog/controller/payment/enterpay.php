<?php
/*

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License as
published by the Free Software Foundation; either version 2 of 
the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


class ControllerPaymentEnterpay extends Controller {
  protected function index() {
   $this->load->language('payment/enterpay');

   $tax_included = $this->config->get('config_tax')==1;  // using the general tax setting
   $tax_total = 0;  // total amount of tax included in the order
   $items_total = 0; // total sum of the items

   $this->load->model('checkout/order');
		
   $this->data['button_confirm'] = $this->language->get('button_confirm');
   $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
   
   if($this->config->get('enterpay_test') == 1) {
      $debug = 1;
      $this->data['enterpay_debug'] = true;
      $this->data['action'] = 'https://test.laskuyritykselle.fi/api/payment/start'; 
   } else {
      $debug = 0; 
      $this->data['enterpay_debug'] = false;
      $this->data['action'] = 'https://laskuyritykselle.fi/api/payment/start';
   }

    $reference = $this->add_checksum($this->config->get('enterpay_reference') . $this->session->data['order_id'] . (time()%99999));  // short but unique within the day

    $params = array(
        "merchant" => $this->config->get('enterpay_merchant'),
        "identifier_merchant" =>$this->session->data['order_id'],
        "reference" => $reference,
        "currency" => 'EUR',
        "url_return" => HTTP_SERVER.'index.php?route=payment/enterpay/callback&oid=' . $order_info['order_id'],
        "version" => $this->config->get('enterpay_enterpayversion'),
        "key_version" =>  $this->config->get('enterpay_sellerkeyver'),
        "locale" => "fi_FI",  // dynamic?
        "billing_address[street]" => $order_info['payment_address_1'] . (empty($order_info['payment_address_2'])?'':' ' . $order_info['payment_address_2']),
        "billing_address[postalCode]" => $order_info['payment_postcode'],
        "billing_address[city]" => $order_info['payment_city'],

        "delivery_address[street]" => $order_info['shipping_address_1'] . (empty($order_info['shipping_address_2'])?'':' ' . $order_info['shipping_address_2']),
        "delivery_address[postalCode]" => $order_info['shipping_postcode'],
        "delivery_address[city]" => $order_info['shipping_city'],
        "debug" => $debug
        );

   $products = $this->cart->getProducts();
   $i = 0;
   foreach ($products as $product) { 
  
     $tax_rate_data = $this->tax->getRates($product['price'], $product['tax_class_id']);
     $tax_rate_data = current($tax_rate_data);
     $tax_rate = $tax_rate_data['rate'];
     $tax_total += $product['quantity'] * ($tax_amount = $tax_rate_data['amount']);
 
     $params["cart_items[$i][name]"] = $product['name'];
     $params["cart_items[$i][identifier]"] = (int) $product['key'];
     $params["cart_items[$i][quantity]"] = $product['quantity'];
     if($tax_included) { 
       $items_total += $product['quantity'] * ($params["cart_items[$i][unit_price_including_tax]"] = number_format(($product['price']+$tax_amount)*100, 0, ".", ""));  
     } else {
       $items_total += $product['quantity'] * ($params["cart_items[$i][unit_price_excluding_tax]"] =
         number_format($product['price']*100, 0, ".", ""));
   }

    $params["cart_items[$i][tax_rate]"] = number_format($tax_rate/100, "2", ".", "");
    $i++;  
  }

 // order total modules

    $total_data = array();
    $total = 0;
    $results = $this->model_setting_extension->getExtensions('total');

    foreach ($results as $key => $value) {
    $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
    }

    array_multisort($sort_order, SORT_ASC, $results);



    foreach ($results as $result) {
       if($result['code'] != 'sub_total' and $result['code'] != 'total' and $result['code'] != 'tax') {
        $total_data = array();
        $total = 0;
        $taxes = array();
        $ot_tax = 0;
        if($this->config->get($result['code'] . '_status')) {
          $this->load->model('total/' . $result['code']);
          $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
        }		



       if(isset($total_data[0]) and $total_data[0]['value'] != 0) {

         foreach($taxes as $tax) {
           $ot_tax += $tax;
         }
         $tax_total += $ot_tax;

         $params["cart_items[$i][name]"] = $total_data[0]['title'];
         $params["cart_items[$i][identifier]"] = $i;
         $params["cart_items[$i][quantity]"] = 1;
         if($tax_included) { 
           $items_total += $params["cart_items[$i][unit_price_including_tax]"] = 
           number_format(($total_data[0]['value']+$ot_tax)*100, 0, ".", "");  
         } else {
           $items_total += $params["cart_items[$i][unit_price_excluding_tax]"] =
           number_format(($total_data[0]['value'])*100, 0, ".", ""); 
         }

          $params["cart_items[$i][tax_rate]"] = number_format(($ot_tax/$total_data[0]['value']), "2", ".", "");
          $i++;
      }
    }  
  }
    
     // catch all block, fixes OC bug in gift voucher
     if(($tax_included and abs($order_info['total']*100 - $items_total) > 1) or 
        (!$tax_included and abs($order_info['total']*100 - $tax_total - $items_total) > 1)) {
     $params["cart_items[$i][name]"] = $this->language->get('text_other_discount');
     $params["cart_items[$i][identifier]"] = $i;
     $params["cart_items[$i][quantity]"] = 1;
     if($tax_included) { 
       $params["cart_items[$i][unit_price_including_tax]"] = 
       number_format(($order_info['total']*100 - $items_total), 0, ".", "");  
     } else {
       $items_total += $params["cart_items[$i][unit_price_excluding_tax]"] =
         number_format(($order_info['total'] - $tax_total)*100 - $items_total, 0, ".", ""); 
     }

     $params["cart_items[$i][tax_rate]"] = 0;

   } // end catch all


  // totals
  if($tax_included) {
      $params["total_price_including_tax"] = number_format($order_info['total']*100, 0, ".", ""); 
    } else {
      $params["total_price_excluding_tax"] = number_format(($order_info['total']-$tax_total)*100, 0, ".", ""); 
    }
		
    // calculate mac 
    ksort($params);
        $hmac_params = array();

        foreach ($params as $k => $v) {
            if ($v !== null && $v !== '' && $k !== 'debug') {
                $hmac_params[$k] = urlencode($k) . '=' . urlencode($v);
            } 
        }

        $str = implode('&', $hmac_params);

        $hmac = hash_hmac('sha512', $str, $this->config->get('enterpay_sellerkey'));

        $this->data['enterpay_params'] = $params;
        $this->data['enterpay_hmac'] = $hmac;

        $this->data['back'] = HTTPS_SERVER . 'index.php?route=checkout/payment';
		
	$this->id = 'payment';

	$this->template = 'default/template/payment/enterpay.tpl';

	$this->render();
      }
	
    public function callback() {
      $this->load->language('payment/enterpay');
      $params = array (
        "version" => $this->request->get['version'],
        "key_version" => $this->request->get['key_version'],
        "status" => $this->request->get['status'],
        "identifier_valuebuy" => $this->request->get['identifier_valuebuy'],
        "identifier_merchant" => $this->request->get['identifier_merchant']);
         
      ksort($params);
      $hmac_params = array();

      foreach ($params as $k => $v) {
        if ($v !== null && $v !== '') {
                $hmac_params[$k] = urlencode($k) . '=' . urlencode($v);
        } 
      }
        
      $hmac_calc = hash_hmac('sha512', implode('&', $hmac_params), $this->config->get('enterpay_sellerkey'));
       
      if(isset($this->request->get['hmac'])) { // not set in error
        $hmac_get = $this->request->get['hmac'];
      } else {
        $hmac_get = '';
      }
      $response = $this->request->get['status'];
 
      if($hmac_get != $hmac_calc or $response == 'failed' or $response == 'canceled') {  // payment not OK
          if($hmac_get != $hmac_calc) {
            $error_text = $this->language->get('text_mac_error'); 
          } else {
            switch ($response) {
              case 'failed': 
                $error_text = $this->language->get('text_failed'); 
                break;
              case 'canceled': 
                $error_text = $this->language->get('text_cancel');
                break;
              default:
                $error_text = $this->language->get('text_unknownfail');
             }
          }
          $this->data['title'] = $this->data['heading_title'] = $this->config->get('enterpay_title');
	  $this->data['continue'] = HTTPS_SERVER . 'index.php?route=checkout/cart';
	  $this->data['text_response'] = $this->language->get('text_response');
	  $this->data['text_return'] = $error_text;
	  $this->data['text_return_wait'] = sprintf($this->language->get('text_failure_wait'), HTTPS_SERVER . 'index.php?route=checkout/cart');
	
	  if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/enterpay_return.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/payment/enterpay_return.tpl';
	} else {
          $this->template = 'default/template/payment/enterpay_return.tpl';
	}
     } else {  // ☺☺☺ The mac matches, response is clear, we have an actual order! Bring in the champange and play the bell! The Party Is ON! ☺☺☺
       $this->language->load('payment/enterpay');
		
      $this->data['title'] = $this->config->get('enterpay_title');

      if (!isset($this->request->server['HTTPS']) || ($this->request->server['HTTPS'] != 'on')) {
        $this->data['base'] = HTTP_SERVER;
	} else {
	  $this->data['base'] = HTTPS_SERVER;
	}
		

	
       $this->data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
			
       $this->data['text_response'] = $this->language->get('text_response');
       $this->data['text_return'] = $this->language->get('text_success');
       $this->data['text_return_wait'] = sprintf($this->language->get('text_success_wait'), HTTPS_SERVER . 'index.php?route=checkout/success');
			
       $this->load->model('checkout/order');

       $this->model_checkout_order->confirm((int)$this->request->get['identifier_merchant'], $this->config->get('enterpay_order_status_id'));
       if($response == 'pending') {
         $this->model_checkout_order->update((int)$this->request->get['oid'], $this->config->get('enterpay_order_status_pending_id'), $this->language->get('text_pending_order_comment') . strip_tags($this->request->get['pending_reasons']), false);
      } else {
        $this->model_checkout_order->update((int)$this->request->get['oid'], $this->config->get('enterpay_order_status_id'),'', false);
	}
       $this->data['continue'] = HTTPS_SERVER . 'index.php?route=checkout/success';
				
	if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/enterpay_return.tpl')) {
	  $this->template = $this->config->get('config_template') . '/template/payment/enterpay_return.tpl';
        } else {
	  $this->template = 'default/template/payment/enterpay_return.tpl';
        }
      }

      $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));

    }  // end function callback
	
	public function callbackdelayed() {

		
	}

  protected function add_checksum($n) {
    if(!ctype_digit($n)) { die("Reference number contains non-numeric characters. $n"); }
    $n = strval($n);
    if(strlen($n) > 19) { die('Reference number too long.'); }
    $weights = array(7,3,1);
    $sum = 0;
    for($i=strlen($n)-1, $j=0; $i>=0; $i--,$j++) {
     $sum += (int) $n[$i] * (int) $weights[$j%3];
    }
    $checksum = (10-($sum%10))%10;
    return $n . $checksum;
  }
   
} // end class
 

