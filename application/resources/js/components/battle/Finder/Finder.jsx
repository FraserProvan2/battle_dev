import React, { Component } from "react";
import ReactDOM from "react-dom";

import * as Utils from "../../../helpers/Utils";

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

            this.checkIfUserHasInvite(this.state.invites);
        });

        this.getInvites();
    }

    render() {
        return (
            <div className="card">
                <div className="card-header">Battle Finder</div>
                <div className="card-body">
                    {this.renderPostInviteButton()}

                    {/* Iterate over invites */}
                    <ul className="list-group">{this.renderInvitesList()}</ul>
                </div>
            </div>
        );
    }

    renderInvitesList() {
        if (this.state.invites.length > 0) {
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
        } else {
            return (
                <div className="d-flex justify-content-center m-4">
                    There are no active invites
                </div>
            );
        }
    }

    renderPostInviteButton() {
        if (!this.state.userHasInvitePosted) {
            return (
                <div className="d-flex flex-row mb-2">
                    <button
                        className="btn btn-primary"
                        onClick={() =>this.postInvite()}
                    >
                        Post Battle Invite
                    </button>
                </div>
            );
        }
    }

    getInvites() {
        axios.get(`invites`).then(response => {
            this.setState({
                invites: response.data
            });

            this.checkIfUserHasInvite(this.state.invites);
        });
    }

    postInvite() {
        if (!this.state.user) return Utils.redirectToLogin();

        axios.get(`invites/post`);

        Utils.reloadPage();
    }

    cancelInvite() {
        axios.get(`invites/cancel`);

        Utils.reloadPage();
    }

    acceptInvite(id) {
        if (!this.state.user) return Utils.redirectToLogin();

        axios.get(`invites/accept/${id}`);

        Utils.reloadPage();
    }

    checkIfUserHasInvite(invites) {
        if (this.state.user) {
            invites.forEach(invite => {
                let has_invite = false;
                if (invite.user_id == this.state.user.id) {
                    has_invite = true;
                }

                this.setState({
                    userHasInvitePosted: has_invite
                });
            });
        }
    }
}
