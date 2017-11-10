import React from 'react';
import ReactDOM from 'react-dom';
import './index.css';
import App from './App';
import Form from './form/Form';
import registerServiceWorker from './registerServiceWorker';

fetch('http://127.0.0.1/my-app/public/scripts/').then(function (data) {
    var testData = data.json();    
    testData.then(function (formConfig) {        
        const myForm = React.createElement(Form, formConfig);
        ReactDOM.render(myForm, document.getElementById('LoginForm'));
    });
});

registerServiceWorker();