import React from "react";
import Paper from "@material-ui/core/Paper";

export default function Notice({message}) {
    return <Paper elevation={2} className={"notice"}>{message}</Paper>;
}