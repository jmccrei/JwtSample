import React from "react";
import {Button} from "@material-ui/core";

export default function Logout({show, onLogout}) {
    if (!show) {
        return null;
    }

    return <div className="logout-btn-container">
        <Button className="logout-btn" color="secondary"
                onClick={onLogout}
                variant="contained">
            Logout
        </Button>
    </div>;
};