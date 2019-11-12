import React, { Component } from 'react';
import ReactDOM from 'react-dom';

export default class Profile extends Component {
    constructor(props) {
        super(props)
 
        this.state = {
            firstName: props.firstName
        }
    }
    
    render() {
        return (
        <div className="card h-100">
            <div className="card-header"> First Name: {this.state.firstName}</div>
            <div className="card-body">
                <p>
                    we will have many details about the user here, github name, avatar
                    attack, defence (how commits and other github data constuct this).

                    battle record etc
                </p>

            </div>
        </div>
        )
    }
}

// Rendering 
const bindToId = 'profile';

if (document.getElementById(bindToId)) {
    const element = document.getElementById(bindToId)
    const props = Object.assign({}, element.dataset) //binds data attributes

    ReactDOM.render(<Profile {...props}/>, element);
}
