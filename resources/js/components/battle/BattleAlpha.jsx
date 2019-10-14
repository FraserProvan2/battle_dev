import React, { Component } from 'react';
import ReactDOM from 'react-dom';

export default class BattleAlpha extends Component {
    constructor(props) {
        super(props)

        this.state = {
            id: this.props.battleId,
            turn: {},
            turn_logs: null,
            player_a: null,
            player_b: null,
        }

        // Debug
        console.log(`Battle ID: ${this.state.id}`);

        // listens battle updates
        window.Echo.private(`App.Battle.${this.state.id}`)
            .listen('TurnEndUpdate', (response) => {
                // update turn state
                this.setState({
                    turn: response.turn,
                    turn_logs: response.turn.battle_frame.turn_summary,
                    player_a: response.turn.battle_frame.player_a,
                    player_b: response.turn.battle_frame.player_b,
                });
            });
            
        // binding `this` to functions
        this.playerAction = this.playerAction.bind(this);
    }
    
    render() {
        return (
        <div className="card">
            <div className="card-header">Battle Alpha</div>
            <div className="card-body">
                {this.renderPlayerActions()}
                {this.renderTurnLogs()}
            </div>
        </div>
        )
    }

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

    renderTurnLogs() {
        if (this.state.turn_logs !== null) {
            return (
                <div>
                    <hr/>
                    <p className="h5">Turn: {this.state.turn.turn_number}</p>
                    <p>
                        {this.state.turn_logs}
                    </p>
                </div>
            );
        }
        return;
    }

    playerAction(playersAction) {
        axios.post(`/battle`, { 
            battle: this.state.id,
            action: playersAction
        });
    }
}

// Rendering 
const bindToId = 'battle';

if (document.getElementById(bindToId)) {
    const element = document.getElementById(bindToId)
    const props = Object.assign({}, element.dataset) //binds data attributes

    ReactDOM.render(<BattleAlpha {...props}/>, element);
}
