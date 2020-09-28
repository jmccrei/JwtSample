import React, {useEffect, useState} from "react";
import Paper from "@material-ui/core/Paper";
import Grid from "@material-ui/core/Grid";
import InputLabel from "@material-ui/core/InputLabel";
import FormControl from "@material-ui/core/FormControl";
import OutlinedInput from "@material-ui/core/OutlinedInput";
import Button from "@material-ui/core/Button";
import * as axios from "axios";
import Notice from "./Notice";

export default function Login({show, onSuccess}) {
    const [data, setData] = useState({initial: true, email: null, password: null, valid: false, error: null});
    let form;

    useEffect(() => {
        if (!data.initial) {
            console.debug('Validate', data);
            form = form || document.querySelector('form#login-form');
            if (form && form.checkValidity()) {
                // form is valid
                if (!data.valid) {
                    setData({...data, valid: true});
                }
            } else {
                if (!!data.valid) {
                    setData({...data, valid: false});
                }
            }
        }
    });

    function onSubmit(e) {
        e.preventDefault();
        e.stopPropagation();

        if (data.valid) {
            const {email, password} = data;
            axios.post(window.Routing.generate('api_login'), {
                email: email,
                password: password
            }).then(res => {
                const resData = res.data || {};
                const jsonData = resData.data || {};

                if (jsonData.hasOwnProperty('token') && jsonData.token !== null) {
                    // valid token returned
                    onSuccess(jsonData);
                } else {
                    // error
                    setData({...data, error: 'Invalid credentials'});
                }
            }).catch(e => {
                setData({...data, error: e.response.data.error || 'Invalid Credentials'});
            });
        }
    }

    function onInputEmail(e) {
        setEmail(e.target.value);
    }

    function setEmail(email) {
        setData({...data, initial: false, email: email});
    }

    function onInputPassword(e) {
        setPassword(e.target.value);
    }

    function setPassword(password) {
        setData({...data, initial: false, password: password});
    }

    function renderError() {
        if (data.error === null || data.error === '') {
            return null;
        }

        return <div className="error">
            {data.error}
        </div>;
    }

    return !!show ? <Paper elevation={2} className={"login-container"}>
        <form onSubmit={onSubmit} id="login-form" noValidate autoComplete="off">
            {renderError()}
            <Grid
                container
                direction="row"
                justify="center"
                alignItems="center"
            >
                <FormControl variant="outlined">
                    <InputLabel htmlFor="email">Email Address</InputLabel>
                    <OutlinedInput type="email"
                                   id="email"
                                   label={"Email Address"}
                                   name="_email"
                                   required
                                   onInput={onInputEmail}/>
                </FormControl>
                <FormControl variant="outlined">
                    <InputLabel htmlFor="password">Password</InputLabel>
                    <OutlinedInput
                        type="password"
                        id="password"
                        name="_password"
                        autoComplete="off"
                        label={"Password"}
                        onInput={onInputPassword}
                        required/>
                </FormControl>
                <Button variant="contained"
                        type="submit"
                        color="primary"
                        disabled={!data.valid}>
                    Login
                </Button>
            </Grid>
        </form>
        <Notice
            message={"Login or Register. If email exists, the email will be logged in, if not, the email will be registered."}/>
    </Paper> : null;
};