import React, { Component } from "react";
import ReactDOM from "react-dom";

export default class Finder extends Component {
    constructor(props) {
        super(props);

        const load_data = JSON.parse(props.load_data);

        this.state = {
            invites: [],
            user: load_data.user,
            userHasInvitePosted: false
        };

        // listen for InviteList event
        window.Echo.channel(`App.Invites`).listen("InviteList", response => {
            this.setState({
                invites: response.invites
            });

            // check if user has invite
            this.state.invites.forEach(invite => {
                let has_invite = false;
                if (invite.user_id == this.state.user.id) {
                    has_invite = true;
                }

                this.setState({
                    userHasInvitePosted: has_invite
                });
            });
        });

        // TODO: get turn data instead of force dispatch
        this.dispatchInviteList();
    }

    render() {
        return (
            <div className="card">
                <div className="card-header">Battle Finder</div>
                <div className="card-body">
                    <div className="row mb-2">
                        <div className="col-md-6">
                            {this.renderPostInviteButton()}
                        </div>
                        <div className="col-md-6">
                            <button
                                className="btn btn-secondary float-right"
                                onClick={this.dispatchInviteList}
                            >
                                <i
                                    className="fa fa-refresh"
                                    aria-hidden="true"
                                ></i>
                            </button>
                        </div>
                    </div>

                    {/* Iterate over invites */}
                    <ul className="list-group">{this.renderInvitesList()}</ul>
                </div>
            </div>
        );
    }

    renderInvitesList() {
        return this.state.invites.map(invite => (
            <li
                key={invite.id}
                className="list-group-item d-flex justify-content-between align-items-center"
            >
                {invite.username}
                
                {/* render Accept invite oR Cancel invite */}
                {this.state.userHasInvitePosted &&
                invite.username == this.state.user.name ? (
                    <button
                        className="btn btn-danger"
                        onClick={this.cancelInvite}
                    >
                        Cancel Invite
                    </button>
                ) : (
                    <button
                        className="btn btn-primary"
                        onClick={() => this.acceptInvite(invite.id)}
                    >
                        Accept
                    </button>
                )}
            </li>
        ));
    }

    renderPostInviteButton() {
        if (!this.state.userHasInvitePosted) {
            return (
                <button className="btn btn-primary" onClick={this.postInvite}>
                    Post Battle Invite
                </button>
            );
        }
    }

    dispatchInviteList() {
        axios.get(`invites/dispatch`);
    }

    postInvite() {
        axios.get(`invites/post`);
    }

    cancelInvite() {
        axios.get(`invites/cancel`);
    }

    acceptInvite(id) {
        axios.get(`invites/accept/${id}`);

        // refresh page
    }
}
