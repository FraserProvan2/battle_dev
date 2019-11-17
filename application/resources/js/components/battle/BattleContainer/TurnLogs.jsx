import React, { Component } from "react";
import Loader from "../_Loader";

export default class TurnLogs extends Component {
    render() {
        let logs = this.props.turn_logs.reverse();
        // const turn_number = this.props.turn_number;

        return logs.map((turn_log, index) => (
            <div key={index}>
                <hr />
                <ul className="list-unstyled text-center text-muted">
                    {this.processTurnLogs(turn_log)}
                </ul>
            </div>
        ));
    }

    processTurnLogs(turn_log) {
        return turn_log.map((log, index) => <li key={index}>{log}</li>);
    }
}
