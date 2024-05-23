import { Navigate, Outlet } from "react-router-dom";
import { ContextProvider, UseStateContext } from "../context/ContextProvider";
import Navbar from "../custom_components/navbar";



export default function DefaultLayout (){


    const {user , token ,remember_me}=UseStateContext();

    if(!token){
      return <Navigate to="/login"/>
    }
    window.onbeforeunload = function() {
        if (!remember_me)
        localStorage.clear();
     }



    const handleLogout=(ev)=>{

        ev.preventDefault();
        console.log("from the handleLogout function ");
        console.log(ev);
    }


    return (
    <div className="container">
        <Navbar/>
<div>
        <header>
            <div>header</div>
            <div>{user.name} </div>
        </header>


<main><Outlet/></main>
<div className="footerDiv footer_div">
<footer className="bg-body-tertiary text-center text-lg-start">

<div className="text-center p-3" style={{backgroundColor:"rgba(0, 0, 0, 0.05)"}}>
{(new Date().getFullYear())} Copyright©:ONNAGA TECH

  </div>

</footer>
</div>

    </div>
    </div>
    )

    }
