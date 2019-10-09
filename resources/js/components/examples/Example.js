import React, { Component } from 'react';
import ReactDOM from 'react-dom';

export default class Example extends Component {
    constructor(props) {
        super(props)
 
        this.state = {
            firstName: props.firstName
        }
    }
    
    render() {
        return (
        <div className="card">
            <div className="card-header">From Example Component</div>
            <div className="card-body">First Name: {this.state.firstName}</div>
        </div>
        )
    }
}

// Rendering 
const bindToId = 'class-component';

if (document.getElementById(bindToId)) {
    const element = document.getElementById(bindToId)
    const props = Object.assign({}, element.dataset) //binds data attributes

    ReactDOM.render(<Example {...props}/>, element);
}
