import React, { Component } from 'react';
import ReactDOM from 'react-dom';

export default class Finder extends Component {
    constructor(props) {
        super(props)
 
        this.state = {}
    }
    
    render() {
        return (
        <div className="card">
            <div className="card-header">Battle Finder</div>
            <div className="card-body">
                <p>
                    This will be a list of users currently looking for a Battle,
                    with an accept button

                    If they click on the button when no auth, they be redirected to the
                    github login.
                </p>

            </div>
        </div>
        )
    }
}

// Rendering 
const bindToId = 'finder';

if (document.getElementById(bindToId)) {
    const element = document.getElementById(bindToId)
    const props = Object.assign({}, element.dataset) //binds data attributes

    ReactDOM.render(<Finder {...props}/>, element);
}
