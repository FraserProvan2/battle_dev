import React from "react";

export default function({ user, player, battle, action }) {
    function playerAction(action) {
        axios
            .post(`/battle`, {
                battle,
                action
            })
            .then(() => {
                dispatchTurn();
            });
    }

    // IF this users username matches the players AND player hasn't actioned yet
    if (user.name == player.username && action == null) {
        return (
            <div className="d-flex">
                <a
                    className="btn btn-primary w-100"
                    onClick={() => playerAction("attack")}
                >
                    Attack
                </a>
                <a
                    className="btn btn-primary w-100 mx-1"
                    onClick={() => playerAction("block")}
                >
                    Block
                </a>
            </div>
        );
    }

    return <div className="p-3 w-100"></div>;
}

function dispatchTurn() {
    axios.get("battle");
}
