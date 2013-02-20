<?php
/*
 * START: Score Sheet String for view and DB storing.
 **/
function xy_ssstring_for_view_byref(&$str) { $str = xy_ssstring_for_view($str); }

function xy_ssstring_for_view($str) {

    $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8'); /* For TextBoxes to interpret html entities. */
    $str = str_replace(array('[nl]', '[comma]', '[and]', '[quote]', '[dquote]'), array("\n", ',', '&', "'", '&quot;'), $str);
    
    return $str;
}
/*
 * END: Score Sheet String for view and DB storing.
 **/
?>