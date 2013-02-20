<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<?php
$cf_url = $this->configXY->URI[0] . '/' . $this->configXY->URI[1];
if($cf_url[strlen($cf_url) - 1] == '/') {
    
    $cf_url = substr ($cf_url, 0, strlen($cf_url) - 1);
}

if($this->session->level == 1) {
    
    $menu[0]['caption'] = 'RTA List';
    $menu[0]['href'] = 'admin/rta';
    
    if($cf_url == 'rta/edit_by_admin') $menu[1]['caption'] = 'Edit RTA';
    else $menu[1]['caption'] = 'File RTA';

    $menu[1]['href'] = 'rta/create';
    $menu[1]['hrefs'] = array('rta/create', 'rta/edit_by_admin', 'rta/edit_by_owner');
    
    $menu[2]['caption'] = 'Calendar';
    $menu[2]['href'] = 'calendar';
    
    $menu[3]['caption'] = 'Questionnaire';
    $menu[3]['href'] = 'sensory/create_test';
    
    if($this->configXY->URI[2] > 0) {

        if(empty($this->configXY->URI['step'])) $menu[3]['tab_info'] = 'Select the step where to manage <b>' . $rta->name . '</b>.';        
    } else $menu[3]['tab_info'] = 'List of questionnaires started and or finished.';

    $menu[4]['caption'] = 'Test compilation';
    $menu[4]['href'] = 'sensory/distribute_test';
    
    $menu[5]['caption'] = 'Monitor';
    $menu[5]['href'] = '#';
    
    $menu[6]['caption'] = 'Reports';
    $menu[6]['href'] = '#';

/* PO, Multi-level 2, Multi-level 3. */
} elseif(in_array($this->session->level, array(2, 6, 7))) {
    
    $menu[0]['caption'] = 'File RTA';
    $menu[0]['href'] = 'rta/create';
    $menu[0]['hrefs'] = array('rta/create', 'rta/edit_by_owner');
    
    $menu[1]['caption'] = 'My RTA';
    $menu[1]['href'] = 'po/rta_by_owner';
    
    $menu[2]['caption'] = 'Calendar';
    $menu[2]['href'] = 'calendar';
}

$t = count($menu);
for($x=0; $x<$t; $x++) {
    
    $menu[$x]['class'] = ' ';
    $menu[$x]['tab_space'] = '';
            
    if(count($menu[$x]['hrefs'])) {
        
        if(in_array($cf_url, $menu[$x]['hrefs'])) {
            
            $menu[$x]['class'] = '  class="here"  ';
            $menu[$x]['tab_space'] = '<ul><li style="height: 16px; font: 11px \'Lucida Grande\'; color : #FFF">' . $menu[$x]['tab_info'] . '</li></ul>';
        }
    }
    else
    if(($cf_url == $menu[$x]['href']) ||
       ($menu[$x]['href'] == 'calendar' && $this->configXY->URI[0] == 'calendar')) {
        
        $menu[$x]['class'] = '  class="here"  ';
        $menu[$x]['tab_space'] = '<ul><li style="height: 16px; font: 11px \'Lucida Grande\'; color : #FFF">' . $menu[$x]['tab_info'] . '</li></ul>';        
    }
}
?>
<ul id="xyNAV"><?php foreach($menu as $item) { ?><li><a <?php echo $item['class']?> href="<?php echo xy_url($item['href'])?>"><?php echo $item['caption']?></a><?php echo $item['tab_space']?></li><?php } ?></ul>