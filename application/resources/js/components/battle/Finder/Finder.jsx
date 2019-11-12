import React, { Component } from 'react';
import ReactDOM from 'react-dom';

export default class Finder extends Component {
    constructor(props) {
        super(props)
 
        this.state = {
            invites: []
        }

        this.getInvites();
    }
    
    render() {
        return (
        <div className="card">
            <div className="card-header">Battle Finder</div>
            <div className="card-body">

                <ul className="list-group">

                    {/* ITERATE OVER this.state.invites */}
                    <li className="list-group-item d-flex justify-content-between align-items-center">
                        Player
                        <button className="btn btn-primary">Accept</button>
                    </li>

                </ul>

            </div>
        </div>
        )
    }

    getInvites() {
        axios.get('invites/getAll').then(response => {
            if (response.data) {
                this.setState({
                    invites: response.data
                });
            }
            console.log(this.state.invites);
        });
    }
}
