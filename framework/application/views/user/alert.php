<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<?php
if(empty($alerts)) {
    
    echo 'There\'s nothing to display here yet.';
    return;
}
?>
<table width="1107" id="table_rta" cellpadding="0" cellspacing="0">
    <tr><th align="center"><div><b>#</b></div></th>
        <th><div>Sample name</div></th>
        <th><div>Content</div></th>
        <th><div>Approved by</div></th>
        <th><div>Approved on</div></th>
    </tr>
    <?php
    foreach($alerts as $row) {
        
        ?>
        <tr><td align="center"><div><?php echo ++$x?></div></td>
            <td><div><a title="<?php echo $row->sample?>" href="<?php echo xy_url('po/rta_view/' . $row->rta_form_id)?>"><b><?php echo $row->sample?></b></a></div></td>
            <td><div><?php echo $row->content?></div></td>
            <td><div><?php echo $row->approved_by?></div></td>
            <td><div><?php echo date('m/d/Y h:iA', strtotime($row->created))?></div></td>
        </tr>
        <?php
    }    
    ?>
</table>
<?php echo $paging?>