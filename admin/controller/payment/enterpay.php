<?php 
class ControllerPaymentEnterpay extends Controller {
	private $error = array(); 

	public function index() {
		$this->load->language('payment/enterpay');

		$this->document->setTitle = $this->language->get('heading_title');

		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->load->model('setting/setting');
			
			$this->model_setting_setting->editSetting('enterpay', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

 			$this->redirect(HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token']);
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		$this->data['text_successful'] = $this->language->get('text_successful');
		$this->data['text_declined'] = $this->language->get('text_declined');
		$this->data['text_off'] = $this->language->get('text_off');
               
		
                $this->data['entry_merchant'] = $this->language->get('entry_merchant');
                if(isset($this->error['merchant'])) {
                   $this->data['error_merchant'] = $this->error('merchant');
                } else {
                   $this->data['error_merchant'] = '';
                }

                $this->data['entry_title'] = $this->language->get('entry_title');
                if(isset($this->error['title'])) {
                   $this->data['error_title'] = $this->error('title');
                } else {
                   $this->data['error_title'] = '';
                }


		$this->data['entry_sellerkey'] = $this->language->get('entry_sellerkey');
		$this->data['entry_sellerkeyver'] = $this->language->get('entry_sellerkeyver');
		$this->data['entry_enterpayversion'] = $this->language->get('entry_enterpayversion');
		$this->data['entry_test'] = $this->language->get('entry_test');
		$this->data['entry_order_status'] = $this->language->get('entry_order_status');	
		$this->data['entry_order_status_pending'] = $this->language->get('entry_order_status_pending');	
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
                $this->data['entry_reference'] = $this->language->get('entry_reference');		

                $this->data['entry_test'] = $this->language->get('entry_test');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');
		

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['merchant'])) {
			$this->data['error_merchant'] = $this->error['merchant'];
		} else {
			$this->data['error_merchant'] = '';
		}

 		if (isset($this->error['sellerkey'])) {
			$this->data['error_sellerkey'] = $this->error['sellerkey'];
		} else {
			$this->data['error_sellerkey'] = '';
		}

 		if (isset($this->error['sellerkeyver'])) {
			$this->data['error_sellerkeyver'] = $this->error['sellerkeyver'];
		} else {
			$this->data['error_sellerkeyver'] = '';
		}

 		if (isset($this->error['enterpayversion'])) {
			$this->data['error_enterpayversion'] = $this->error['enterpayversion'];
		} else {
			$this->data['error_enterpayversion'] = '';
		}

		$this->document->breadcrumbs = array();

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_payment'),
      		'separator' => ' :: '
   		);

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=payment/enterpay&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = HTTPS_SERVER . 'index.php?route=payment/enterpay&token=' . $this->session->data['token'];
		
		$this->data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'];

               if (isset($this->request->post['enterpay_title'])) {
			$this->data['enterpay_title'] = $this->request->post         ['enterpay_title'];
		} else {
			$this->data['enterpay_title'] = $this->config->get('enterpay_title');
		}


		if (isset($this->request->post['enterpay_merchant'])) {
			$this->data['enterpay_merchant'] = $this->request->post['enterpay_merchant'];
		} else {
			$this->data['enterpay_merchant'] = $this->config->get('enterpay_merchant');
		}
		
               if (isset($this->request->post['enterpay_enterpayversion'])) {
			$this->data['enterpay_enterpayversion'] = $this->request->post['enterpay_enterpayversion'];
		} else {
			$this->data['enterpay_enterpayversion'] = $this->config->get('enterpay_enterpayversion');
		}
		

		if (isset($this->request->post['enterpay_sellerkey'])) {
			$this->data['enterpay_sellerkey'] = $this->request->post['enterpay_sellerkey'];
		} else {
			$this->data['enterpay_sellerkey'] = $this->config->get('enterpay_sellerkey');
		}
	
		if (isset($this->request->post['enterpay_sellerkeyver'])) {
			$this->data['enterpay_sellerkeyver'] = $this->request->post['enterpay_sellerkeyver'];
		} else {
			$this->data['enterpay_sellerkeyver'] = $this->config->get('enterpay_sellerkeyver');
		}

		if (isset($this->request->post['enterpay_order_status_id'])) {
			$this->data['enterpay_order_status_id'] = $this->request->post['enterpay_order_status_id'];
		} else {
			$this->data['enterpay_order_status_id'] = $this->config->get('enterpay_order_status_id');
		}
		

		if (isset($this->request->post['enterpay_order_status_pending_id'])) {
			$this->data['enterpay_order_status_pending_id'] = $this->request->post['enterpay_order_status_pending_id'];
		} else {
			$this->data['enterpay_order_status_pending_id'] = $this->config->get('enterpay_order_status_pending_id');
		}

		if (isset($this->request->post['enterpay_test'])) {
			$this->data['enterpay_test'] = $this->request->post['enterpay_test'];
		} else {
			$this->data['enterpay_test'] = $this->config->get('enterpay_test');
		}
		
// REMOVE ORDER STATUS
		if (isset($this->request->post['enterpay_reference'])) {
			$this->data['enterpay_reference'] = $this->request->post['enterpay_reference'];
		} else {
			$this->data['enterpay_reference'] = $this->config->get('enterpay_reference'); 
		}

             if (isset($this->request->post['enterpay_test'])) {
			$this->data['enterpay_test'] = $this->request->post['enterpay_test'];
		} else {
			$this->data['enterpay_test'] = $this->config->get('enterpay_test'); 
		}

		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		


		if (isset($this->request->post['enterpay_geo_zone_id'])) {
			$this->data['enterpay_geo_zone_id'] = $this->request->post['enterpay_geo_zone_id'];
		} else {
			$this->data['enterpay_geo_zone_id'] = $this->config->get('enterpay_geo_zone_id'); 
		} 
		
		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['enterpay_status'])) {
			$this->data['enterpay_status'] = $this->request->post['enterpay_status'];
		} else {
			$this->data['enterpay_status'] = $this->config->get('enterpay_status');
		}
		
		if (isset($this->request->post['enterpay_sort_order'])) {
			$this->data['enterpay_sort_order'] = $this->request->post['enterpay_sort_order'];
		} else {
			$this->data['enterpay_sort_order'] = $this->config->get('enterpay_sort_order');
		}
		
		$this->id       = 'content';
		$this->template = 'payment/enterpay.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}
// validate response
	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/enterpay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
               if (!$this->request->post['enterpay_title']) {
			$this->error['title'] = $this->language->get('error_title');
		}		

		if (!$this->request->post['enterpay_merchant']) {
			$this->error['merchant'] = $this->language->get('error_merchant');
		}

		if (!$this->request->post['enterpay_sellerkey']) {
			$this->error['sellerkey'] = $this->language->get('error_sellerkey');
		}

		if (!$this->request->post['enterpay_sellerkeyver']) {
			$this->error['sellerkeyver'] = $this->language->get('error_sellerkeyver');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
?>
