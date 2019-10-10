import React, { Component } from 'react';
import ReactDOM from 'react-dom';

export default class BattleAlpha extends Component {
    constructor(props) {
        super(props)

        this.state = {
            id: this.props.battleId
        }

        // Debug
        console.log(`Battle ID: ${this.state.id}`);

        // listens to pusher channel for updates
        window.Echo.private(`App.Battle.${this.state.id}`).listen('Test', (response) => {
            console.log(response);
        });
    }
    
    render() {
        return (
        <div className="card">
            <div className="card-header">Battle Alpha</div>
            <div className="card-body">

            </div>
        </div>
        )
    }
}

// Rendering 
const bindToId = 'battle';

if (document.getElementById(bindToId)) {
    const element = document.getElementById(bindToId)
    const props = Object.assign({}, element.dataset) //binds data attributes

    ReactDOM.render(<BattleAlpha {...props}/>, element);
}
