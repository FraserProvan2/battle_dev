import React, { Component } from "react";
import ReactDOM from "react-dom";
import Battle from "./_Battle";
import Finder from "./Finder/Finder";
import Loader from "./_Loader";
import axios from "axios";

export default class App extends Component {
    constructor(props) {
        super(props);

        this.state = {
            battle_id: null,
            turn: null
        };

        // check if in battle, if so set battle ID
        this.tryGetBattle();
    }

    render() {
        // if in battle, render battle
        if (this.state.battle_id) {
            return (
                <Battle
                    battle_id={this.state.battle_id}
                    load_data={this.props.loadData}
                    turn={this.state.turn}
                />
            );
        }

        // else render battle finder
        return <Finder load_data={this.props.loadData} />;
    }

    // check if users in battle, set ID if so
    tryGetBattle() {
        axios.get("battle/check").then(response => {
            if (response.data.battle) {
                this.setState({
                    battle_id: response.data.battle.id,
                    turn: response.data.turn
                });
            }
        });
    }
}

// Rendering
const bindToId = "battle";

if (document.getElementById(bindToId)) {
    const element = document.getElementById(bindToId);
    const props = Object.assign({}, element.dataset); //binds data attributes

    ReactDOM.render(<App {...props} />, element);
}
