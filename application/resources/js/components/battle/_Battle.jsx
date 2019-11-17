import React, { Component } from "react";
import ReactDOM from "react-dom";

import Loader from "./_Loader";
import Scene from "./BattleContainer/Scene";

import * as Utils from "../../helpers/Utils";

export default class Battle extends Component {
    constructor(props) {
        super(props);

        const load_data = JSON.parse(props.load_data);
        let turn = this.props.turn;

        this.state = {
            id: this.props.battle_id,
            turn: turn.id,
            turn_logs: turn.battle_frame.turn_summary,
            action_a: turn.player_a_action,
            action_b: turn.player_b_action,
            player_a: turn.battle_frame.player_a,
            player_b: turn.battle_frame.player_b,
            winner: null,
            assets: load_data.assets,
            user: load_data.user
        };

        // listens battle updates
        window.Echo.private(`App.Battle.${this.state.id}`)
            // turn updates
            .listen("TurnEndUpdate", response => {
                this.updateTurnState(response.turn);
            })
            // listen to victor
            .listen("AnnounceWinner", response => {
                this.setState({
                    winner: response.winner_username
                });

                // refresh to go back to battle finder
                setTimeout(function() {
                    Utils.reloadPage();
                }, 5000);
            });
    }

    componentWillReceiveProps() {
        this.dispatchTurn();
    }

    render() {
        const { turn } = this.state;
        if (!turn) return <Loader />;
        return (
            <div className="card h-100">
                <div className="card-header">Battle</div>
                <div className="card-body">
                    <Scene {...this.state} />
                    {this.renderTurnLogs()}
                </div>
            </div>
        );
    }

    renderTurnLogs() {
        if (this.state.turn_logs !== null) {
            return (
                <div>
                    <hr />
                    <p className="small py-1">
                        Turn {this.state.turn.turn_number}
                    </p>
                    <p>{this.state.turn_logs}</p>
                    {this.state.winner && (
                        <h5 className="text-success  text-center">
                            {this.state.winner} Wins!!!
                        </h5>
                    )}
                </div>
            );
        }
        return;
    }

    updateTurnState(turn) {
        this.setState({
            turn: turn,
            turn_logs: turn.battle_frame.turn_summary,
            player_a: turn.battle_frame.player_a,
            player_b: turn.battle_frame.player_b,
            action_a: turn.player_a_action,
            action_b: turn.player_b_action
        });
    }

    dispatchTurn() {
        axios.get("battle");
    }
}
