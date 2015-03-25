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
class ModelPaymentEnterpay extends Model {
  	public function getMethod($address, $total) {
		$this->load->language('payment/enterpay');
		
		if ($this->config->get('enterpay_status')) {
      		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('enterpay_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
			
			if (!$this->config->get('enterpay_geo_zone_id')) {
        		$status = true;
      		} elseif ($query->num_rows) {
      		  	$status = true;
      		} else {
     	  		$status = false;
			}	
      	} else {
			$status = false;
		}
		
		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'         => 'enterpay',
        		'title'      => $this->config->get('enterpay_title'),
			'sort_order' => $this->config->get('enterpay_sort_order')
      		);
    	}
   
    	return $method_data;
  	}
}
?>
