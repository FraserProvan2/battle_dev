import React, { Component } from 'react';
import ReactDOM from 'react-dom';

export default class Finder extends Component {
    constructor(props) {
        super(props)
 
        this.state = {
            players: []
        }
    }
    
    render() {
        return (
        <div className="card">
            <div className="card-header">Battle Finder</div>
            <div className="card-body">

            <ul className="list-group">
                {this.renderPlayers}
                <li className="list-group-item d-flex justify-content-between align-items-center">
                  Player
                  {/* <span className="badge badge-primary badge-pill">14</span> */}
                </li>
            </ul>

            </div>
        </div>
        )
    }
}
