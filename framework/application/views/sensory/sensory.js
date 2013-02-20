var Q = new function() {
    
    this.id = 0;
    this.code_combination = {};
    this.code_distribution = {};
    this.batch_no = 1;
    this.respondents = 0;
    
    /* START: For STEP 2 */
    this.codes_1 = []; /* Primary codes. */
    this.codes_2 = []; /* Secondary codes. */
    this.control_codes_index = 0;
    this.rta_id = 0;
    this.type_of_test = '';    
    /* END: For STEP 2 */
};