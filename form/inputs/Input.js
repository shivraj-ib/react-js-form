import React from 'react';

class Input extends React.Component {

    constructor(props) {
        super(props);
        this.state = {value: this.props.value};
        this.handleChange = this.handleChange.bind(this);
        this.handleBlur = this.handleBlur.bind(this);
    }    

    handleChange(event) {
        if(this.props.subtype == 'file'){
            this.props.saveValue(this.props.name,event.target.files[0]);
        }
        this.setState({value: event.target.value});
    }

    handleBlur(event) {
        //update parent form value
        if(this.props.subtype != 'file')
        this.props.saveValue(this.props.name,event.target.value);
    }   

    render() {
        return (
                <div className={this.props.wrapperClass + " " + (typeof this.props.validation !== 'undefined' && this.props.validation != '' ? 'has-error':'')}>
                    {this.props.label != '' ? <label htmlFor={this.props.name}>{this.props.label}</label> : ''}
                    <input 
                        type={this.props.subtype}
                        name={this.props.name} 
                        id={this.props.id} 
                        className={this.props.className}
                        placeholder = {this.props.placeholder}
                        value={this.state.value} onChange={this.handleChange} onBlur={this.handleBlur} 
                    />
                    {typeof this.props.validation !== 'undefined' && this.props.validation != '' ? <small className="error-message">{this.props.validation}</small>:''}
                </div>

                );
    }
}

export default Input;
