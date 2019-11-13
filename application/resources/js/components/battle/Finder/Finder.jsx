import React, { Component } from 'react';
import ReactDOM from 'react-dom';

export default class Finder extends Component {
    constructor(props) {
        super(props)
 
        this.state = {
            invites: []
        }

        // listen for InviteList event
        window.Echo.channel(`App.Invites`)
            .listen('InviteList', (response) => {
                this.setState({
                        invites: response.invites
                    });
                });
                
        this.dispatchInviteList();
    }
    
    render() {
        return (
            <div className="card">
                <div className="card-header">Battle Finder</div>
                <div className="card-body">

                    <div className="flex-row mb-2">
                        <button className="btn btn-primary ">Post Battle Invite</button>
                        <button 
                            className="btn btn-secondary float-right" 
                            onClick={this.dispatchInviteList}
                        >
                            <i className="fa fa-refresh" aria-hidden="true"></i>
                        </button>
                    </div>

                    {/* Iterate over invites */}
                    <ul className="list-group">
                        {this.renderInvitesList()}
                    </ul>

                </div>
            </div>
        )
    }

    renderInvitesList() {
        return this.state.invites.map((invite) => 
            (
                <li 
                    key={invite.id}
                    className="list-group-item d-flex justify-content-between align-items-center"
                >
                    {invite.username}
                    <button className="btn btn-primary" onClick={this.acceptInvite(invite.id)}>Accept</button>
                </li>
            )
        );
    }

    dispatchInviteList() {
        axios.get('invites/dispatch');
    }

    acceptInvite (id) {
        axios.get(`invites/accept/${id}`);
    }
}
