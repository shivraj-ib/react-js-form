/**
 * Create Form
 */
import React from 'react';
import Input from './inputs/Input';
import CheckBox from './inputs/CheckBox';
import Radio from './inputs/Radio';
import Select from './inputs/Select';
import TextArea from './inputs/TextArea';
import Submit from './inputs/Submit';
import Validation from './validations/Validation';
class Form extends React.Component {
    constructor(props) {
        super(props);
        this.validation = new Validation();        
        this.validation_rules = [];
        this.state = {is_dirty: false, errors: {}, values: {},form_errors:[],form_success:''};
        this.handleSubmit = this.handleSubmit.bind(this);
        this.updateValue = this.updateValue.bind(this);
    }

    componentWillMount() {
        if (this.props.fields !== 'undefined') {
            const fields = this.props.fields;
            var def_values = [];
            for (var key in fields) {
                if (fields.hasOwnProperty(key)) {
                    def_values[fields[key].name] = fields[key].value;
                    //set validation rules                    
                    if (fields[key].validation !== 'undefined' && fields[key].validation != '') {                        
                        this.validation_rules[fields[key].name] = fields[key].validation;
                    }
                }
            }
            //set initial form field values
            this.setState({
                values: def_values
            });
        }
    }

    updateValue(field_name, value) {       
        if (field_name != '') {
            this.setState({
                values: {...this.state.values, [field_name]: value}
            },function callBack(){ this.validate(field_name, value)});           
        }
    }

    validate(field_name, value) {
        //check if validation is applicable
        if (this.validation_rules[field_name] !== 'undefined' && this.validation_rules[field_name] != '') {
            //check for each validation rule
            const rules = this.validation_rules[field_name];            
            for (var rule in rules) {
                if (this.validation[rule](value) === false) {                    
                    this.state.errors[field_name] = rules[rule];
                    this.setState({
                        errors: this.state.errors
                    });
                    //break on first validation error                    
                    break;
                } else {
                    //remove error if validation is success.
                    delete this.state.errors[field_name];
                    this.setState({
                        errors: this.state.errors
                    });
                }                
            }
        }
    }

    getValidationDetails(field_name) {
        return this.state.errors[field_name];
    }

    handleSubmit(event) {
        //clear old success message
        this.setState({form_success:''});        
        if(typeof this.state.values !== 'undefined' && typeof this.state.values != ''){
            const fvalues = this.state.values;
            for(var fname in fvalues){
                this.validate(fname,fvalues[fname]);                
            }
        }
        if(false === this.isEmptyObject(this.state.errors)){
            this.setState({form_errors:[this.props.formdetails.validation_error_message]});
        }else{
            this.setState({form_errors:[]});
        }      
        if(this.isEmptyObject(this.state.errors)){
            if(this.props.formdetails.isAjax == true 
               && this.props.formdetails.action != ''
               && this.props.formdetails.action != '#'
            ){
                event.preventDefault();
                this.makeRequest(event);
            }
        }else{
            event.preventDefault();
        }     
    }
    
    isEmptyObject(object){
        //check if there is a validation error
        for(var key in object) {
        if(object.hasOwnProperty(key))
            return false;
        }
        return true;
    }
    
    makeRequest(event){
        var data = new FormData(event.target);
        var object = this;        
        fetch(this.props.formdetails.action, {
            method: 'POST',            
            //body: JSON.stringify(this.state.values)
            body: data
        }).then(function (response) {
            if(response.status == 200){
               data = response.json();
               data.then(function(result){
                   if(result.success == 1){
                       object.setState({form_success:result.message});
                   }else if(result.success == 0){
                       object.setState({form_errors:[result.message]});
                       //set validation errors
                       if(typeof result.validation_errors !== 'undefined'){
                           object.setState({errors:result.validation_errors});
                       }
                   }else{
                      object.setState({form_errors:['Unable to process your request. Please try later.']}); 
                   }
               });
            }else{
                object.setState({form_errors:['Invalid response. Please try later.']});
            }            
        });
    }

    render() {
        
        const fieldMap = {
                    input : Input,
                    checkbox : CheckBox,
                    radio: Radio,
                    select : Select,
                    textarea : TextArea,
                    submit : Submit
                }

        var childrenWithProps = [];
        var p = this.props.fields;
        for (var key in p) {
            if (p.hasOwnProperty(key)) {
                p[key]['key'] = key;
                p[key]['saveValue'] = this.updateValue;
                p[key]['validation'] = this.getValidationDetails(p[key]['name']);                
                childrenWithProps.push(React.createElement(fieldMap[p[key].type], p[key]));
            }
        }
        
        
        
        
        var form_errros = [];        
        if(false === this.isEmptyObject(this.state.form_errors)){
            for(var key in this.state.form_errors) {
                if(this.state.form_errors.hasOwnProperty(key))
                    form_errros.push(<small className="error-message" key={key}>{this.state.form_errors[key]}</small>);
            }
        }
        
        var form_success = '';
        
        if(this.state.form_success != ''){
            form_success = <small className='success-message'>{this.state.form_success}</small>;
        }

        return (
                <div className="formContainer">
                    <h1>{this.props.formdetails.title}</h1>                    
                    {form_errros}
                    {form_success}
                    <form 
                    method={this.props.formdetails.method} 
                    action={this.props.formdetails.action} 
                    onSubmit={this.handleSubmit}
                    encType={this.props.enctype}
                    noValidate
                    >                                           
                        {childrenWithProps}                            
                    </form>
                </div>
                );
    }
}

export default Form;


