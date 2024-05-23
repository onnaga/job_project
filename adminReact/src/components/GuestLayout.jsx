import { Navigate, Outlet } from "react-router-dom";
import { UseStateContext } from "../context/ContextProvider";

export default function GuestLayout (){

    const {token} = UseStateContext();

    if(token){
        return <Navigate to="/"/>
    }

    return (
    <div id="main_content">
<Outlet/>


<div className="footerDiv footer_div">
<footer className="bg-body-tertiary text-center text-lg-start">

<div className="text-center p-3" style={{backgroundColor:"rgba(0, 0, 0, 0.05)"}}>
{(new Date().getFullYear())} Copyright©:ONNAGA TECH

  </div>

</footer>
</div>

    </div>


    )

    }
