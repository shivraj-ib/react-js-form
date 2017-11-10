import React from 'react';
import Input from './inputs/Input';
import CheckBox from './inputs/CheckBox';
import Radio from './inputs/Radio';
import Select from './inputs/Select';
import TextArea from './inputs/TextArea';
import Submit from './inputs/Submit';
class Form extends React.Component {
    constructor(props) {
        super(props);
        this.state = {};
        this.handleSubmit = this.handleSubmit.bind(this);
    }
    
    componentDidMount(){
        
    }

    handleSubmit(event) {
        event.preventDefault();        
        return false;
    }

    render() {        
        return (
                <div className="formContainer">
                    <h1>{this.props.formdetails.title}</h1>
                    <form method={this.props.formdetails.method} action={this.props.formdetails.action} onSubmit={this.handleSubmit}>                        
                    </form>
                </div>
                );
    }
}

export default Form;


