import React, {useEffect, useState} from 'react';
import '../scss/app.scss';
import Login from "./Login";
import Sequencer from "./Sequencer";
import Logout from "./Logout";

function App() {
    const [state, setState] = useState({token: null, email: null, showLogin: true});

    useEffect(() => {
        if (state.token === null) {
            if (window.localStorage.hasOwnProperty('seq_token')) {
                setState({
                    ...state,
                    token: window.localStorage.getItem('seq_token'),
                    email: window.localStorage.getItem('seq_email'),
                    showLogin: false
                });
            }
        }
    });

    function handleOnSuccess({email, token}) {
        window.localStorage.setItem('seq_token', token);
        window.localStorage.setItem('seq_email', email);

        setState({
            ...state,
            token: token || null,
            email: email || null,
            showLogin: (token || null) === null
        });
    }

    function handleOnLogout() {
        window.localStorage.removeItem('seq_token');
        window.localStorage.removeItem('seq_email');

        setState({
            ...state,
            token: null,
            email: null,
            showLogin: true
        });
    }

    return (
        <div className="app">
            <Login show={state.showLogin} onSuccess={handleOnSuccess}/>
            <Sequencer token={state.token} email={state.email} onUnAuthorized={handleOnLogout}/>
            <Logout show={!state.showLogin} onLogout={handleOnLogout}/>
        </div>
    );
}

export default App;
