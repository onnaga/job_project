import { useEffect, useState,useRef } from "react";
import axiosClient from "../axios_client"
import LoadingComponent from "../custom_components/loadingCompnent";
import RatingComponent from "../custom_components/RatingComponent";
import { Link } from "react-router-dom";


export default function specializations (){

    const [Specializations, setSpecializations] = useState([]);
    const [errors , setErrors] = useState([]);
    const [numberUsersNull, setNumberUsersNull] = useState(0);
    const [numberCompaniesNull, setNumberCompaniesNull] = useState(0);
    const [isLoading , setIsLoading] = useState(true);

    const doRequest =()=>{

        setIsLoading(true)
        axiosClient.get('/specializations',{
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer '+localStorage.getItem('ACCESS_TOKEN')
        },
    }).then(
    (response)=>{
      setIsLoading(false)
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
        //validation work fine
        else{
                console.log(response);
                setSpecializations(response.data.specializations)
                console.log(response.data.specializations);
                setNumberUsersNull(response.data.Num_users_null)
                console.log(response.data.Num_users_null);
                setNumberCompaniesNull(response.data.Num_cmpanies_null)
        }
    },
    (err)=>{
      setIsLoading(false)
        if (err.response.status==401) {
            localStorage.clear()

        }

        console.log(err);
    }
    )
    }
    window.onload=doRequest;
    useEffect(()=>{
        doRequest();


    },[])
    return (
        <div className='navbarMain'>
    <div>
<nav className="navbar navbar-light bg-light">
  <div className="container-fluid">
    <a className="navbar-brand">Specializations</a>
  </div>
</nav>

    </div>
      {isLoading?<LoadingComponent/>:  null }<div>


      {errors.map((e,index)=>{
      return (
          <div key={index} className="alert alert-danger alert_div" role="alert"   >
            {e}
          </div>
          )
      })}
<div>

        <h5>
            {`the number of Companies without specialization is ${numberCompaniesNull}`}
        </h5>
        <h5>
            {`the number of Companies without specialization is ${numberUsersNull}`}
        </h5>
<table className="table table-striped">

  <thead>
    <tr>
    <th scope="col">specialization id</th>
      <th scope="col">specialization name</th>
      <th scope="col">companies</th>
      <th scope="col">number of companies</th>
      <th scope="col">users</th>
      <th scope="col">number of users</th>
    </tr>
  </thead>
  <tbody>
  {Specializations.map((obj,index)=>{
return (
    <tr key={index}>
    <th scope="row">{obj.id}</th>
    <td>{obj.specialization}</td>
    <td>{obj.companies.map((company,index)=>{

       return( `${company.name} `)
    })}</td>
<td>{obj.number_of_companies}</td>
<td>{obj.users.map((user,index)=>{

        return (`${user.name} `)
    })}</td>
<td>{obj.number_of_users}</td>
  </tr>
    )
})}
  </tbody>
</table></div>




          </div>
          </div>
    )


    }
