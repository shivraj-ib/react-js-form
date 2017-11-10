import React from 'react';
class TextArea extends React.Component {

    constructor(props) {
        super(props);
        this.state = {value: this.props.value};
        this.handleChange = this.handleChange.bind(this);        
    }

    handleChange(event) {
        this.setState({value: event.target.value},function callBack(){this.props.saveValue(this.props.name,this.state.value);});        
    }

    render() {
        return (
                <div className={this.props.wrapperClass + (typeof this.props.validation !== 'undefined' && this.props.validation != '' ? ' has-error':'')}>
                    {this.props.label != '' ? <label htmlFor={this.props.name}>{this.props.label}</label> : ''}
                    <textarea                        
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

export default TextArea;
