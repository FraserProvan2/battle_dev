import React, { Component } from 'react';
import ReactDOM from 'react-dom';

export default class Game extends Component {
    constructor(props) {
        super(props)

        this.state = {
            id: this.props.battle_id,
            turn: {},
            turn_logs: null,
            player_a: null,
            player_b: null,
            winner: null
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

        this.updateBattleData(); // load current turn data
            
        // binding `this` to functions
        this.playerAction = this.playerAction.bind(this);
    }
    
    render() {
        return (
        <div className="card h-100">
            <div className="card-header">Battle Alpha</div>
            <div className="card-body">
                {this.renderPlayerActions()}
                {this.renderTurnLogs()}
            </div>
        </div>
        )
    }

    // render action buttons
    renderPlayerActions() {
        return (
            <div className="row">
                <div className="col-md-6">
                    {this.renderPlayerStats(this.state.player_a)}
                    <a className="btn btn-primary w-100" onClick={() => this.playerAction("attack")}>
                        Attack
                    </a>
                </div>
                <div className="col-md-6">
                    {this.renderPlayerStats(this.state.player_b)}
                    <a className="btn btn-primary w-100" onClick={() => this.playerAction("block")}>
                        Block
                    </a>
                </div>
            </div>
        )
    }

    // render player stats
    renderPlayerStats(player) {
        if (player) {
            return (
                <div>
                    <span className="h4">{player.username}</span>
                    <ul className="list-unstyled">
                        <li className="small">HP: {player.hp}</li>
                        <li className="small">Damage: {player.damage}</li>
                        <li className="small">Speed: {player.speed}</li>
                    </ul>
                </div>
            );
        } 
        return;
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

    // attempts to register players action
    playerAction(playersAction) {
        axios.post(`/battle`, { 
            battle: this.state.id,
            action: playersAction
        });
    }
}
