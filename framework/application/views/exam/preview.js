var EXAM = new function() {
    
    this.rta_id = 0;
    this.screen_code = 0;
    this.one_ss_only = true;
    this.screen_code_next = 0;
    
    this.go = function(step) {
        
        if(! step) step = 1;
        
        window.location.href = DOCROOT +'exam/preview/'+ this.rta_id +'/'+ this.screen_code +'/'+ step;
    }
    
    this.close = function() {
        
        window.close();
    }
}