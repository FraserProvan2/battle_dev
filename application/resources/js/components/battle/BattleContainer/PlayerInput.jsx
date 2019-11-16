import React from "react"

export default function({user, player, battle}) {
    function playerAction(action) {
        axios.post(`/battle`, { 
            battle,
            action
        });
    }

    if (user.name == player.username) {
        return (
            <div className="d-flex">
                <a className="btn btn-primary w-100" onClick={() => playerAction("attack")}>
                    Attack
                </a>
                <a className="btn btn-primary w-100 mx-1" onClick={() => playerAction("block")}>
                    Block
                </a>
            </div>
        )
    }
    return null;
}
