<?php defined('BASEPATH') or exit('No direct script access allowed');

/* 
 * Title : Employee Model Class
 * Author: Armande Bayanes (tuso@programmerspride.com)
 * Description: Handles Logging-in and Registration, etc ...
 **/

class Calendar_model extends CI_Model {

    function __construct() {
        
        parent::__construct();
    }
    
    public function loadCellContent($year = '', $month = '') {
        
        $url = $this->config->item('XY')->DOCROOT . 'calendar/?target=day&amp;date=' . ($year . '-' .$month . '-{day}');
        
        return '
            <div class="content_cell">
                <div class="pad5">
                    <table cellpadding="0" cellspacing="0" width="100%">
                        <tr><td style="border: 0; padding-top: 15px"><div>{content}</div></td>
                            <td style="border: 0" valign="top">
                                <div><a href="' . $url . '">{day}</a></div>                                
                            </td>
                        </tr>
                    </table>                    
                </div>
            </div>
        ';
    }
    
    public function loadCellNoContent() {
        
        return '
            <div class="content_cell">
                <div class="pad5">
                    <div style="text-align: right">{day}</div>                    
                </div>
            </div>
        ';
    }
    
    public function loadCellContentToday($year = '', $month = '') {
        
        $url = $this->config->item('XY')->DOCROOT . 'calendar/?target=day&amp;date=' . ($year . '-' .$month . '-{day}');
        
        return '
            <div class="content_cell" style="background: #EFEFEF">
                <div class="pad5">
                
                    <div style="text-align: right; color: #990000"><a href="' . $url . '"><sup>Today</sup></a> <span style="font-weight: bold; font-size: 20px">{day}</span></div>
                    
                    <div>{content}</div>
                </div>
            </div>
        ';
    }
    
    public function loadCellNoContentToday() {
        
        return '
            <div class="content_cell">
                <div class="pad5">
                    <div style="text-align: right; color: #990000"><sup>Today</sup> <span style="font-weight: bold; font-size: 20px">{day}</span></div>                    
                </div>
            </div>
        ';
    }
    
    public function loadHeader() {
        
        return '<div style="text-align: center; padding: 5px; font-weight: bold">{heading}</div>';
    }
    
    public function loadDayHeader() {
        
        return '<div style="text-align: center; padding: 5px; font-weight: bold">{week_day}</div>';
    }
}

/* End of file calendar_model.php */
/* Location: ./application/models/calendar_model.php */