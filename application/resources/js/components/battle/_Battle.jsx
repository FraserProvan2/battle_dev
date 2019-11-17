import React, { Component } from "react";
import ReactDOM from "react-dom";

import Loader from "./_Loader";
import Scene from "./BattleContainer/Scene";

import * as Utils from "../../helpers/Utils";
import TurnLogs from "./BattleContainer/TurnLogs";

export default class Battle extends Component {
    constructor(props) {
        super(props);

        const load_data = JSON.parse(props.load_data);
        let turn = this.props.turn;

        this.state = {
            id: this.props.battle_id,
            turn: turn.id,
            turn_logs: [], //turn.battle_frame.turn_summary
            turn_number: turn.turn_number,
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
                this.addTurnLogs(response.turn.battle_frame.turn_summary);
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
                    {/* Battle Scene */}
                    <Scene {...this.state} />

                    {/* Turn Logs/Winner */}
                    <div className="w-100 mt-3">Battle Logs:</div>
                    {this.announceWinner()}
                    <TurnLogs {...this.state} />
                </div>
            </div>
        );
    }

    updateTurnState(turn) {
        this.setState({
            turn: turn,
            turn_number: turn.turn_number,
            player_a: turn.battle_frame.player_a,
            player_b: turn.battle_frame.player_b,
            action_a: turn.player_a_action,
            action_b: turn.player_b_action
        });
    }

    addTurnLogs(logs) {
        if (logs.length > 0) {
            this.state.turn_logs.push(logs);
        }
    }

    announceWinner() {
        if (this.state.winner !== null) {
            return (
                <h5 className="text-success  text-center">
                    {this.state.winner} Wins!!!
                </h5>
            );
        }
    }

    dispatchTurn() {
        axios.get("battle");
    }
}
