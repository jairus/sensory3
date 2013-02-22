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
        
        $url = xy_url('calendar/?target=day&amp;date=' . ($year . '-' .$month . '-{day}'));
        
        return '
            <div class="content_cell">
                <div class="pad5">
                    <div style="text-align: right; color: #990000"><a href="' . $url . '">{day}</a></div>
                    <div>{content}</div>
                </div>
            </div>
        ';
    }
    
    public function loadCellNoContent() {
        
        return '
            <div class="content_cell">
                <div class="pad5">
                    <div class="cell_no_content">{day}</div>
                </div>
            </div>
        ';
    }
    
    public function loadCellContentToday($year = '', $month = '') {
        
        $url = xy_url('calendar/?target=day&amp;date=' . ($year . '-' .$month . '-{day}'));
        
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
                <div class="pad5 cell_no_content_today">
                    <div><sup>Today</sup> <span>{day}</span></div>
                </div>
            </div>
        ';
    }
    
    public function loadHeader() {
        
        return '<div class="header_month">{heading}</div>';
    }
    
    public function loadDayHeader() {
        
        return '<div class="header_day">{week_day}</div>';
    }
    
    public function getWeekNumbers($month, $year) {
        
        /**
         * How Many Weeks in a Year?
         * 1 Year = 365 or 366 (Leap year) days.
         * 1 Week = 7 days.
         * So,
         *      Number of days in a Year / Number of days in a Week
         *      365 or 366 / 7
         *      52.something
         * Get the "floor" to have "52"
         **/
        
        $grouped_by_week_number = array();
        $nof_days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        for($x=1; $x<=$nof_days_in_month; $x++) {
            
            $dd = str_pad($x, 2, '0', STR_PAD_LEFT);
            $tmp_date = "$month/$dd/$year";
            
            /* Get the "week number" where the current date falls into. */
            $number_of_the_week = date('W', strtotime($tmp_date));
            
            /* Group it for checking later. */
            $grouped_by_week_number[$number_of_the_week][] = $tmp_date;            
        }
        
        return $grouped_by_week_number;
    }
    
    public function getWeekNumbersAll($year) {
        
        /**
         * How Many Weeks in a Year?
         * 1 Year = 365 or 366 (Leap year) days.
         * 1 Week = 7 days.
         * So,
         *      Number of days in a Year / Number of days in a Week
         *      365 or 366 / 7
         *      52.something
         * Get the "floor" to have "52"
         **/
        
        $grouped_by_week_number = array();
        
        for($x=1; $x<=12; $x++) {
            
            $month = str_pad($x, 2, '0', STR_PAD_LEFT);
            $nof_days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            
            for($y=1; $y<=$nof_days_in_month; $y++) {
            
                $dd = str_pad($y, 2, '0', STR_PAD_LEFT);
                $tmp_date = "$month/$dd/$year";

                /* Get the "week number" where the current date falls into. */
                $number_of_the_week = date('W', strtotime($tmp_date));

                /* Group it for checking later. */
                $grouped_by_week_number[$number_of_the_week][] = $tmp_date;            
            }
        }
        
        return $grouped_by_week_number;
    }
}

/* End of file calendar_model.php */
/* Location: ./application/models/calendar_model.php */