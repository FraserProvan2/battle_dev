import React from "react";

import PlayerInput from "./PlayerInput";

function PlayerCard({ player, assets, action }) {
    const { avatar, username, speed, hp, damage } = player;
    const { hp_stat_icon, speed_stat_icon, attack_stat_icon } = assets;

    let turn_status = "Awaiting action.";
    if (action) turn_status = "Actioned.";

    return (
        <div className="d-flex flex-column align-items-center">
            <h4>{username}</h4>
            <div>
                <img
                    src={avatar}
                    className="rounded-circle battle-player-img"
                />
            </div>
            <ul className="list-unstyled d-flex justify-content-space-between my-2">
                <li className="mx-1 d-flex align-items-center">
                    <img
                        src={hp_stat_icon}
                        className="battle-player-stat-icon"
                    />
                    {hp}
                </li>
                <li className="mx-1 d-flex align-items-center mx-3">
                    <img
                        src={attack_stat_icon}
                        className="battle-player-stat-icon"
                    />
                    {damage}
                </li>
                <li className="mx-1 d-flex align-items-center">
                    <img
                        src={speed_stat_icon}
                        className="battle-player-stat-icon"
                    />
                    {speed}
                </li>
            </ul>
            <small className="text-muted">{turn_status}</small>
        </div>
    );
}

export default function({
    assets,
    id,
    player_a,
    player_b,
    action_a,
    action_b,
    user
}) {
    return (
        <div className="row">
            <div className="col-md-6">
                <PlayerCard
                    assets={assets}
                    player={player_a}
                    action={action_a}
                />
                <PlayerInput
                    battle={id}
                    user={user}
                    player={player_a}
                    action={action_a}
                />
            </div>
            <div className="col-md-6">
                <PlayerCard
                    assets={assets}
                    player={player_b}
                    action={action_b}
                />
                <PlayerInput
                    battle={id}
                    user={user}
                    player={player_b}
                    action={action_b}
                />
            </div>
        </div>
    );
}
