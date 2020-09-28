import React, {useEffect, useState} from "react";
import Paper from "@material-ui/core/Paper";
import {Button} from "@material-ui/core";
import * as axios from "axios";
import FormControl from "@material-ui/core/FormControl";
import InputLabel from "@material-ui/core/InputLabel";
import Grid from "@material-ui/core/Grid";
import Input from "@material-ui/core/Input";

export default function Sequencer({token, email, onUnAuthorized}) {
    if (token === undefined || token === null) {
        return null;
    }

    const [state, setState] = useState({current: -1, newCurrent: 0});

    useEffect(() => {
        if (state.current === -1) {
            // we need the current value
            axios.get(Routing.generate('api_current'), {
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            }).then(setXhrResponse).catch(onUnAuthorized);
        }
    });

    function getNext() {
        axios.get(Routing.generate('api_next'), {
            headers: {
                'Authorization': 'Bearer ' + token
            }
        }).then(setXhrResponse);
    }

    function setCurrent() {
        axios.put(Routing.generate('api_current'), {
            current: state.newCurrent
        }, {
            headers: {
                'Authorization': 'Bearer ' + token
            }
        }).then(setXhrResponse);
    }

    function setXhrResponse(res) {
        setState({...state, current: parseInt((res.data || {}).data || 0)});
    }

    function setNewCurrent(e) {
        let idx = parseInt(e.target.value);
        if (idx < 0) {
            idx = 0;
            e.target.value(0);
        }

        setState({...state, newCurrent: idx});
    }

    if (state.current === -1) {
        return <Paper elevation={2} className={"sequencer-container loading"}>LOADING</Paper>;
    }

    return <Paper elevation={2} className={"sequencer-container"}>
        <Grid container spacing={1} direction="row" alignContent="center" justify="center">
            <Grid item xs={12} className={"title"}>
                <h1>Sequencer</h1>
                {email ? <small>{email}</small> : null}
            </Grid>
            <Grid item xs={12} className={"current-container"}>
                <h3>Current: <span className={"current"}>{state.current}</span></h3>
            </Grid>
            <Grid item xs={12}>
                <Button onClick={getNext} variant={"contained"} color="primary">Next Sequence</Button>
            </Grid>
            <Grid item xs={12} className={"setter-container"}>
                <FormControl variant="outlined">
                    <InputLabel htmlFor="current">Current</InputLabel>
                    <Input
                        type="number"
                        id="current"
                        name="_current"
                        autoComplete="off"
                        defaultValue={0}
                        label={"Current"}
                        onChange={setNewCurrent}
                        required/>
                </FormControl>
                <Button variant="contained"
                        className="set-btn"
                        color="primary"
                        onClick={setCurrent}
                        disabled={state.newCurrent === state.current}>
                    Set
                </Button>
            </Grid>
        </Grid>
    </Paper>;
};