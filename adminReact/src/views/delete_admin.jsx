
import { useState ,useRef } from "react"
import axiosClient from "../axios_client";

export default function delete_admin(){

    const emailRef = useRef();

  const [errors , setErrors]= useState([]);
  const [Delets , setDelets]= useState([]);
    const doOnSubmit = (e)=>{
        e.preventDefault()
        const Data = {
            email : emailRef.current.value,
        }

        console.log(Data);
        axiosClient.post('delete_admin' ,Data, {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer '+localStorage.getItem('ACCESS_TOKEN')
            },
        }).then((response)=>{
            console.log(Data);
            console.log(response);

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
        else{
            console.log(response);

            setDelets((previosAdds)=>{

                return [...previosAdds,'the user Deleted sucssefully']
            });
        setTimeout(() => {

            setDelets([])
            }, 2000);

        }

        },
    (err)=>{
        if (err.response.status==401) {
            localStorage.clear()
        }
        if (err.response!=null && err.response.status>=500) {

            setErrors((previosErr)=>{

              console.log(err);

              return [...previosErr,`${err.response.statusText}  please try again later OR check IF email is wrong Or Deleted`]
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
    return (

            <div id="container">

            <form onSubmit = {doOnSubmit}className="formLogin">
              <h2>Delete Admin</h2>

              <div className="form-group">
                <label htmlFor="exampleInputEmail1">Email address</label>
                <input   ref = {emailRef} type="email" className="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email" required/>
                <small id="emailHelp" className="form-text text-muted"><p>The admin who have the email will delete </p></small>
              </div>
              <button type="submit" className="btn btn-danger login_btn">Delete</button>
            </form>

            {errors.map((e,index)=>{
    return (
        <div key={index} className="alert alert-danger alert_div" role="alert"   >
          {e}
        </div>
        )

    })}

{Delets.map((del,index)=>{
return (
    <div key={index} className="alert alert-info alert_div" role="alert"   >
      {del}
    </div>
    )
})}

            </div>
        )



    }
