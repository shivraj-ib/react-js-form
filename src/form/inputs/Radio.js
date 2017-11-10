import React from 'react';
class Radio extends React.Component {

    constructor(props) {
        super(props);
        this.state = {value: this.props.value, errorClass: ''};
        this.handleChange = this.handleChange.bind(this);        
    }

    handleChange(event) {
        const value = event.target.value;
        this.setState({value: value},function callBack(){this.props.saveValue(this.props.name,value);});        
    }

    render() {
        const checkBoxes = this.props.options.map((option,index) =>
                <div className={this.props.wrapperClass} key={index}>
                    <label>
                        <input type="radio" 
                        id={this.props.id} 
                        name={this.props.name} 
                        className={this.props.className} 
                        value={option.value} 
                        onChange={this.handleChange}
                        checked={this.state.value == option.value ? true:false}                        
                        />
                        {option.label}
                    </label>                                           
                </div>
        );
    
        return (
               <div className={this.props.wrapperClass+(typeof this.props.validation !== 'undefined' && this.props.validation != '' ? ' has-error':'')}> 
                {(this.props.label != '') ? <label>{this.props.label}</label>:''}
                {checkBoxes}
                {typeof this.props.validation !== 'undefined' && this.props.validation != '' ? <small className="error-message">{this.props.validation}</small>:''}
               </div>         
             );
    }
}

export default Radio;

