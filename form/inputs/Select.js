import React from 'react';
class Select extends React.Component {

    constructor(props) {
        super(props);        
        this.state = {selected : (this.props.multiple == true ? this.props.value:this.props.value.toString()), errorClass: ''};
        this.handleChange = this.handleChange.bind(this);        
    }

    handleChange(event) {
        const value = event.target.value;        
        var values = this.state.selected;
        if(this.props.multiple == true){
            if(values.indexOf(value) != -1){
                values.splice(values.indexOf(value),1)
            }else{
                if(value != ""){
                    values.push(value);
                }else{
                    values = [];
                }
            }
            this.setState({selected:values},function callBack(){this.props.saveValue(this.props.name,this.state.selected)});
        }else{
            this.setState({selected:value},function callBack(){this.props.saveValue(this.props.name,this.state.selected)});            
        }
    }

    render() {
        
        const options = this.props.options.map((option,index) => <option key={index} value={option.value}>{option.label}</option>);
        return (
                <div className={this.props.wrapperClass+(typeof this.props.validation !== 'undefined' && this.props.validation != '' ? ' has-error':'')}>
                    {this.props.label != '' ? <label htmlFor={this.props.name}>{this.props.label}</label> : ''}
                        <select 
                        id={this.props.id} 
                        name={this.props.name} 
                        className={this.props.className}                        
                        onChange={this.handleChange}
                        value={this.state.selected}
                        multiple={this.props.multiple == true ? true:false}
                        >
                        {options}
                        </select>
                        {typeof this.props.validation !== 'undefined' && this.props.validation != '' ? <small className="error-message">{this.props.validation}</small>:''}
                       
                </div>
           );
    }
}

export default Select;
