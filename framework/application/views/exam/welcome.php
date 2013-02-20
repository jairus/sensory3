<?php defined('BASEPATH') or exit('No direct script access allowed')?>
<div class="wrapper_inner">
    
    <div style="font-size: 20px; margin: 50px auto auto auto; min-height: 300px; width: 90%; vertical-align: middle">
        
        <pre class="fntWrap" style="font-size: 20px; color: #555"><?php echo $exam->welcome_text?></pre>
        <pre class="fntWrap" style="font-size: 30px; margin-top: 100px"><?php echo $exam->instruction?></pre>
        
    </div>
    
    <div style="margin: 50px 0 10px 0">

        <div style="float: right"><input id="proceed" type="button" value="Proceed to Exam" /></div>
        <div style="clear: both"></div>
        
    </div>
    
</div>