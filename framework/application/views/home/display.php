<style type="text/css">
    .calendar {
            font-family: Arial; 
            font-size: 12px; 
            height: 100%;
    }
    table.calendar {
            margin: auto; border-collapse: collapse;
    }
    
    .calendar .days td {
            color: #FFF;
            width: 120px; height: 80px; padding: 4px;
            border: 1px solid #C0C2B2;
            vertical-align: top;
            background-color: #6E0000;             
    }
    .calendar .days td:hover {
            background-color: #6B686E;
    }
    .calendar .highlight {
            font-weight: bold; 
            color: #FF9500;
            font-size: 16px;
    }
    .calendar .days .content{
        font-size: 10px;
    }    
    .content a{
        text-decoration: none;
        color: #FF9B00;
        font-style: italic;
        font-family: Verdana;
    }
    
</style>
<?php
    echo $calendar;
?>
