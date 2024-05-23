import { useRef , useState } from "react"
import axiosClient from "../axios_client"
import { UseStateContext } from "../context/ContextProvider"
import LoadingComponent from "../custom_components/loadingCompnent";

export default function add_admin (){
const nameRef = useRef()
const emailRef = useRef()
const passwordRef = useRef()
const confirmPasswordRef = useRef()
const {token,user} = UseStateContext()
const [errors , setErrors] = useState([]);
const [adds , setAdds] = useState([]);

    const doOnSubmit = (ev)=>{
        ev.preventDefault();
        const payload = {
            name : nameRef.current.value,
            email : emailRef.current.value,
            password : passwordRef.current.value,
            password_confirmation : confirmPasswordRef.current.value,
        }
        console.log(payload)

        axiosClient.post('add_admin' ,payload, {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer '+localStorage.getItem('ACCESS_TOKEN')
            },
        })
        .then((response)=>{

            console.log(response);
        //there we check if there is a validation errors
        if(response.data.errors!=null){
            console.log(`the token is ${token}`);
            console.log(`the token is ${user}`);
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

    console.log(response);

        setAdds((previosAdds)=>{

            return [...previosAdds,'the user added sucssefully']
        });
    setTimeout(() => {

        setAdds([])
        }, 2000);

    }

}
     , (err)=>{
        if (err.response.status==401) {
            localStorage.clear()
        }
      if (err.response!=null && err.response.status>=500) {

        setErrors((previosErr)=>{

          console.log(err);

          return [...previosErr,`${err.response.statusText}  please try again later`]
      })

      }else{
        setErrors((previosErr)=>{
            console.log(err);
          return [...previosErr,`${ err.response.data.message}`]

      })

      }

    setTimeout(() => {

      setErrors([])
      }, 3000);

   }
    )
}



debugger
    return (

        <div id="container">

        <form onSubmit = {doOnSubmit}className="formLogin">
          <h2>Add some Admins</h2>
          <div className="form-group">
            <label htmlFor="exampleInputEmail1">Full name</label>
            <input    ref = {nameRef}  type="text" className="form-control" id="exampleInputFulllname"  placeholder="Enter Name" required/>

          </div>
          <div className="form-group">
            <label htmlFor="exampleInputEmail1">Email address</label>
            <input   ref = {emailRef} type="email" className="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email" required/>
            <small id="emailHelp" className="form-text text-muted"><p>We'll never share your email</p></small>
          </div>
          <div className="form-group">
            <label htmlFor="exampleInputPassword1">Password</label>
            <input   ref = {passwordRef} type="password" className="form-control" id="exampleInputPassword1" placeholder="Password"  name="password" required/>
          </div>

          <div className="form-group">
            <label htmlFor="exampleInputPassword1">Confirm Passowrd</label>
            <input  ref = {confirmPasswordRef}  type="password" className="form-control" id="exampleInputPassword2" placeholder="Confirm Password"  name="password_confirmation" required/>
          </div>

          <button type="submit" className="btn btn-dark login_btn">Add</button>



        </form>

        {errors.map((e,index)=>{
return (
    <div key={index} className="alert alert-danger alert_div" role="alert"   >
      {e}
    </div>
    )

})}

{adds.map((add,index)=>{
return (
    <div key={index} className="alert alert-info alert_div" role="alert"   >
      {add}
    </div>
    )
})}
        </div>
    )
debugger
    }
