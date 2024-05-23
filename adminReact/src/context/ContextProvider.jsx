import { createContext, useContext, useState } from "react";

const stateContext =createContext({
    user: null,
    token : null,
    remember_me : null,
    setUser:()=>{},
    setToken:()=>{},
    setRemember_me:()=>{}
});


export const ContextProvider =({children})=>{
    const [user, _setUser]= useState(localStorage.getItem('User'));
    const [remember_me, _setRemember]= useState(localStorage.getItem('Remember'));
    const [token ,_setToken]=useState(localStorage.getItem('ACCESS_TOKEN'));
    const setUser = (use)=>{
        localStorage.setItem('User',JSON.stringify(use))
        _setUser(use)
        debugger
    }

    const  setRemember_me = (Rem)=>{
        localStorage.setItem('Remember',Rem)
        _setRemember(Rem)
        debugger
    }

    const setToken=(token)=>{
        _setToken(token);
        if(token!=null){
            localStorage.setItem('ACCESS_TOKEN',token);
            console.log(`inside if token is : ${token}`)
            console.log(`inside if user is : ${user}`)
        }
        else{
            localStorage.removeItem('ACCESS_TOKEN');
            console.log(`inside else token is : ${token}`)
            console.log(`inside else user is : ${user}`)
        }


    }


    return (
        <stateContext.Provider value={{
            user,
            setUser,
            token,
            setToken,
            remember_me,
            setRemember_me,
        }}>


        {children}

        </stateContext.Provider>
    )
};

export const UseStateContext=()=>{return useContext(stateContext)};
