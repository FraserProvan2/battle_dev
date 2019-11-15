import React, { Component } from 'react';
import ReactDOM from 'react-dom';

import Loader from "./_Loader";
import Scene from "./BattleContainer/Scene"

export default class Battle extends Component {
    constructor(props) {
        super(props)

        const load_data = JSON.parse(props.load_data);

        this.state = {
            id: this.props.battle_id,
            turn: null,
            turn_logs: null,
            player_a: null,
            player_b: null,
            winner: null,
            assets: load_data.assets,
            user: load_data.user,
        }

        // listens battle updates
        window.Echo.private(`App.Battle.${this.state.id}`)
            // turn updates
            .listen('TurnEndUpdate', (response) => {
                if (response.turn.battle_frame) {
                    this.setState({
                        turn: response.turn,
                        turn_logs: response.turn.battle_frame.turn_summary,
                        player_a: response.turn.battle_frame.player_a,
                        player_b: response.turn.battle_frame.player_b,
                    });
                }
            })
            // listen to victor
            .listen('AnnounceWinner', (response) => {
                this.setState({
                    winner: response.winner_username
                })
            });

            // TODO: get turn data instead of force dispatch
            this.updateBattleData(); // load current turn data
        }
    
    render() {
        const {turn} = this.state
        if (!this.state.turn) return <Loader />
        return (
            <div className="card h-100">
                <div className="card-header">Battle Alpha</div>
                <div className="card-body">
                    <Scene {...this.state} />
                    {this.renderTurnLogs()}
                </div>
            </div>
        )
    }

    // render logs
    renderTurnLogs() {
        if (this.state.turn_logs !== null) {
            return (
                <div>
                    <hr/>
                    <p className="small py-1">Turn {this.state.turn.turn_number}</p>
                    <p>
                        {this.state.turn_logs}
                    </p>
                    {this.state.winner &&
                        <h5 className="text-success  text-center">{this.state.winner} Wins!!!</h5>
                    }
                </div>
            );
        }
        return;
    }

    // check if users in battle, set ID if so
    updateBattleData() {
        axios.get(`battle/dispatch/${this.props.battle_id}`);
    }
}
