import React from 'react';

class Submit extends React.Component {

    constructor(props) {
        super(props);                     
    }   

    render() {
        return (
                <button 
                    type="submit" 
                    className={this.props.className} 
                    value={this.props.value}
                    name={this.props.name} 
                    id={this.props.id} 
                >
                    {this.props.label}
                </button>
               );
    }
}

export default Submit;

