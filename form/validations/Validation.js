class Validation {
    constructor() {

    }
    validate(callBack, value) {
        if (false === this[callBack](value)) {
            return false;
        }
        return true;
    }
    
    isEmptyObject(object){
        //check if there is a validation error
        for(var key in object) {
        if(object.hasOwnProperty(key))
            return false;
        }        
        return true;
    }

    //validation rule function
    required(value) {
        const type = typeof value;        
        switch (type) {
            case 'string':
                return (value == '') ? false : true;
                break;
            case 'number':
                return (value == '') ? false : true;
                break;
            case 'object':
                return !(this.isEmptyObject(value));                
                break;
            case 'array':                
                return (value.length > 0) ? true:false;
                break;
            case 'undefined':
                return false;
        }
    }
    
    //validate file upload field
    frequired(value){
       return value instanceof File; 
    }

    email(value) {
        return (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(value));
    }  

}

export default Validation;