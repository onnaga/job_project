import { useRef, useState } from "react";
import axiosClient from "../axios_client";
import { UseStateContext } from "../context/ContextProvider";


export default function login (){
    const {setUser, setToken,setRemember_me} = UseStateContext()
    const emailRef = useRef();
    const passwordRef = useRef();
    const checkboxRef =useRef();

    const [errors , setErrors] = useState([]);

const doOnSubmit =(e)=>{
    e.preventDefault()
    const data = {
        email:e.nativeEvent.srcElement[0].value,
        password: e.nativeEvent.srcElement[1].value,
        remember_me:e.nativeEvent.srcElement[2].checked
    }

    axiosClient.post('/login',data)
    .then((response)=>{

//there we check if there is a validation errors
        if(response.data.errors!=null){
               //there we reset the error div style to display normaly
            const arrOfErrors = Object.entries(response.data.errors)
            arrOfErrors.map((err,index)=>{
                setErrors((previosErr)=>{

                    return [...previosErr,err[1][0]]
                })
            });
//there we delete the errors from the array
            setTimeout(() => {

            setErrors([])
            }, 2000);

        }

//if the validation worked
else{
    console.log(response.data.rememberMe);
  setUser(response.data.user)
  setToken(response.data.authorisation.token)
  setRemember_me(response.data.rememberMe)

}
    } , (err)=>{
      if (err.response && err.response.status>=500) {

        setErrors((previosErr)=>{

          console.log(err);
          return [...previosErr,`${err.response.statusText}  please try again later`]
      })

      }else{
        setErrors((previosErr)=>{
          return [...previosErr,`${ err.response.data.message}`]
      })
      }

    setTimeout(() => {

      setErrors([])
      }, 3000);

   }
    )

}
return (
//   <div id="main_content">
<div id="container">

<form onSubmit = {doOnSubmit}className="formLogin">
  <h2>login to your account</h2>
  <div className="form-group">
    <label htmlFor="exampleInputEmail1">Email address</label>
    <input ref={emailRef} type="email" className="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email"/>
    <small id="emailHelp" className="form-text text-muted"><p>We'll never share your email with anyone else.</p></small>
  </div>
  <div className="form-group">
    <label htmlFor="exampleInputPassword1">Password</label>
    <input ref={passwordRef} type="password" className="form-control" id="exampleInputPassword1" placeholder="Password"/>
  </div>
  <div className="form-group form-check checkMe">

    <input  ref={checkboxRef} type="checkbox" className="form-check-input" id="exampleCheck1"/>
    <label className="form-check-label" htmlFor="exampleCheck1">Check me out</label>
  </div>
  <button type="submit" className="btn btn-dark login_btn">Login</button>

  {errors.map((e,index)=>{
return (
    <div key={index} className="alert alert-danger alert_div" role="alert"   >
      {e}
    </div>
    )

})}
</form>

</div>
// </div>
)

}
