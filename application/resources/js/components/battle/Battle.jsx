import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import Game from './_Game';
import Finder from './_Finder';
import Loader from './_Loader';
import axios from 'axios';

export default class Battle extends Component {
    constructor(props) {
        super(props)

        this.state = {
            battle_id: null
        }

        // check if in battle, if so set battle ID
        this.tryGetBattle(); 

        // listen for if battle starts

        // TEMP
            // axios.post(`/battle`, { 
            //     battle: 1,
            //     action: "attack"
            // });
    }

    render() {
        // if in battle, render battle
        if (this.state.battle_id) {
            return <Game 
                battle_id={this.state.battle_id}
                load_data={this.props.loadData}
            />
        } 

        // else render battle finder
        return <Finder />
    }

    // check if users in battle, set ID if so
    tryGetBattle() {
        axios.get('battle/check').then(response => {
            if (response.data.battle) {
                this.setState({
                    battle_id: response.data.battle.id
                });
            }
        });
    }
}

// Rendering 
const bindToId = 'battle';

if (document.getElementById(bindToId)) {
    const element = document.getElementById(bindToId)
    const props = Object.assign({}, element.dataset) //binds data attributes

    ReactDOM.render(<Battle {...props}/>, element);
}
