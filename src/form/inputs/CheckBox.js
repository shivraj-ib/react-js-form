import React from 'react';

class CheckBox extends React.Component {
    constructor(props) {
        super(props); 
        this.state = {selected : this.props.value.map(String)};
        this.handleChange = this.handleChange.bind(this);        
    }

    handleChange(event) {
         const checked = event.target.checked;
         const value = event.target.value;
         var temp = this.state.selected;
         if(checked){
            temp.push(value.toString());
         }else{
             temp.splice(temp.indexOf(value.toString()),1);
         }         
         this.setState({
             selected:temp
         },function callBack(){this.props.saveValue(this.props.name,this.state.selected);});
    }
    
    isChecked(value){
        if(typeof this.state.selected !== 'undefined' && this.state.selected.indexOf(value.toString()) != -1){
            return true;
        }
        return false;
    }

    render() {
        
        const isGroup = (this.props.options.length > 1) ? true:false;
        
        const checkBoxes = this.props.options.map((option,index) =>
                <div className={this.props.wrapperClass} key={index}>
                    <label>
                        <input type="checkbox"                        
                        id={this.props.id} 
                        name={this.props.name+(isGroup ? "[]":"")} 
                        className={this.props.className} 
                        value={option.value}                        
                        onChange={this.handleChange}
                        checked={this.isChecked(option.value)}
                        />
                        {option.label}
                    </label>                                           
                </div>
        );
    
        return (
               <div className={(typeof this.props.validation !== 'undefined' && this.props.validation != '' ? 'has-error':'')}> 
                {(this.props.label != '') ? <label>{this.props.label}</label>:''}
                {checkBoxes}
                {typeof this.props.validation !== 'undefined' && this.props.validation != '' ? <small className="error-message">{this.props.validation}</small>:''}        
               </div>         
             );
    }
}

export default CheckBox;
