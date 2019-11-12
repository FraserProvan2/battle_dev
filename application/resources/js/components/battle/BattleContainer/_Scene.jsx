import React from 'react'

import PlayerInput from "./_PlayerInput"

function Loader() {
    return (
        <div className="d-flex flex-1 justify-content-center text-dark py-4">
            <i className="fas fa-3x fa-circle-notch fa-spin"></i>
        </div>
    )
}

function PlayerCard({player, assets}) {
    const {avatar, username, speed, hp, damage} = player
    const {hp_stat_icon, speed_stat_icon, attack_stat_icon} = assets
    return (
        <div className="d-flex flex-column align-items-center">
            <h4>{username}</h4>
            <div>
                <img src={avatar} className="rounded-circle battle-player-img"/>
            </div>
            <ul className="list-unstyled d-flex justify-content-space-between my-2">
                <li className="mx-1 d-flex align-items-center">
                    <img src={hp_stat_icon} className="battle-player-stat-icon" />
                    {hp}
                </li>
                <li className="mx-1 d-flex align-items-center mx-3">
                    <img src={attack_stat_icon} className="battle-player-stat-icon" />
                    {damage}
                </li>
                <li className="mx-1 d-flex align-items-center">
                    <img src={speed_stat_icon} className="battle-player-stat-icon" />
                    {speed}
                </li>
            </ul>
        </div>
    )
}

export default function({player_a, player_b, assets, user, id}) {
    if (!player_a || !player_b) return <Loader />
    return (
        <div className="row">
            <div className="col-md-6">
                <PlayerCard assets={assets} player={player_a} />
                <PlayerInput user={user} player={player_a} battle={id} />
            </div>
            <div className="col-md-6">
                <PlayerCard assets={assets} player={player_b} />
                <PlayerInput user={user} player={player_b} battle={id} />
            </div>
        </div>
    )
}
