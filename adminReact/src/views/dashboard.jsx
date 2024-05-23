import { useEffect, useState,useRef } from "react";
import axiosClient from "../axios_client"
import LoadingComponent from "../custom_components/loadingCompnent";
import RatingComponent from "../custom_components/RatingComponent";
import { Link } from "react-router-dom";

export default  function dashboard(){
    var getRequestUrl
    const [data , setData] = useState([]);
    const [errors , setErrors] = useState([]);
    const [number , setNumber] = useState(0);
    const [Endednumber , setNumberEnded] = useState(0);
    const [Endeddata , setDataEnded] = useState([]);
    const [isLoading , setIsLoading] = useState(true);
    const [company_id , setCompanyId] = useState(false);
    // const [company_id , setCompanyId] = useState(false);
    // const [company_id , setCompanyId] = useState(false);
    const inputRef = useRef();

    const doRequest =()=>{
        getRequestUrl = `companies?company_id=${company_id}`
        setIsLoading(true)
        axiosClient.get(getRequestUrl,{
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
            //if the response is for companies
            if (response.data.data) {
                console.log(response);
                setData(response.data.data)
                console.log(response.data.data);
                setNumber(response.data.number)
                console.log(response.data.number);

            }
            //if it for offers in the company
            else{
                console.log(response);
                setData(response.data.recent_offers)

                setNumber(response.data.number_of_recent)

                setDataEnded(response.data.ended_offers)

                setNumberEnded(response.data.number_of_ended)
            }
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
        console.log(company_id);

    },[company_id])


    const formSubmit = (e)=>{
        e.preventDefault()
        setCompanyId(e.nativeEvent.srcElement[0].value)
        console.log(e.nativeEvent.srcElement[0].value);


    }
    const companyClick =()=>{
        setCompanyId(false)
    }








    return (
        <div className='navbarMain'>
    <div>
<nav className="navbar navbar-light bg-light">
  <div className="container-fluid">
    <a className="navbar-brand">Dashboard</a>
    <button onClick={companyClick}className="btn btn-outline-secondary btn-sm" type="button">show companies</button>
    <button className="btn btn-outline-secondary btn-sm" type="button">

    <Link  className = "link-light Links"to="/specializations" >show specializations</Link>
    </button>
    <button onClick={companyClick}className="btn btn-outline-secondary btn-sm" type="button">show users </button>
    <form onSubmit={formSubmit} className="d-flex">
      <input ref ={inputRef} className="form-control me-2" type="search" placeholder="put company id" aria-label="Search"/>
      <button className="btn btn-outline-secondary btn-sm" type="submit">show offers</button>
    </form>
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
      {company_id?
      // render the Offers
      <div>
      <h5>
                  {`the number of recent Offers is ${number}`}
              </h5>
      <table className="table table-striped">

        <thead>
          <tr>
          <th scope="col">offer id</th>
            <th scope="col">company id </th>
            <th scope="col">Work start at</th>
            <th scope="col">official holidays</th>
            <th scope="col">period</th>
            <th scope="col">salary</th>
            <th scope="col">specialization_wanted</th>
            <th scope="col">the days</th>

            <th scope="col">the job</th>
            {/* ///////////////////////////////////////////////////////////////// */}
            {<th scope="col">the rate</th>}
            {
            <th scope="col">offer end at</th>
            }
            {
            <th scope="col">is affective</th>
           }

          </tr>
        </thead>
        <tbody>
        {data.map((obj,index)=>{
      return (
          <tr key={index}>
          <th scope="row">{obj.id}
          </th>
          <td>{obj.company_id}</td>
          <td>{obj.hour_begin}</td>
      <td>{obj.official_holidays}</td>
      <td>{obj.period}</td>
      <td>{obj.salary}</td>
      <td>{obj.specialization_wanted}</td>
      <td>{obj.the_days}</td>
      <td>{obj.the_job}</td>
      {/* ?////////////////////////////////////////////////////////////// */}
      <RatingComponent star_rate={obj.rating} />
      {
        <td>{obj.offer_end_at}</td>
       }
            {
        <td>{'now affective'}</td>
            }


        </tr>
          )
      })}
        </tbody>
      </table>
      <h5>
                  {`the number of Old Offers is ${Endednumber}`}
              </h5>
      <table className="table table-striped">

        <thead>
          <tr>
          <th scope="col">offer id</th>
            <th scope="col">company id</th>
            <th scope="col">Work start at</th>
            <th scope="col">official holidays</th>
            <th scope="col">period</th>
            <th scope="col">salary</th>
            <th scope="col">specialization_wanted</th>
            <th scope="col">the days</th>
            <th scope="col">the job</th>
            {/* ////////////////////////////////////////////////////////////////// */}
            {<th scope="col">the rate</th>}
            {
            <th scope="col">offer end at</th>
            }
            {
            <th scope="col">is affective</th>
           }

          </tr>
        </thead>
        <tbody>
        {Endeddata.map((obj,index)=>{
      return (
          <tr key={index}>
          <th scope="row">{obj.id}</th>
          <td>{obj.company_id}</td>
          <td>{obj.hour_begin}</td>
      <td>{obj.official_holidays}</td>
      <td>{obj.period}</td>
      <td>{obj.salary}</td>
      <td>{obj.specialization_wanted}</td>
      <td>{obj.the_days}</td>
      <td>{obj.the_job}</td>
      {/* //////////////////////////////////////////////////////////////////////// */}
      {<RatingComponent star_rate={obj.rating} />}
      {
        <td>{obj.offer_end_at}</td>
       }
            {
        <td>{'not affective'}</td>
            }
        </tr>
          )
      })}
        </tbody>
      </table>


      </div>
:      // render the companies
<div>
<h5>
            {`the number of Companies is ${number}`}
        </h5>
<table className="table table-striped">

  <thead>
    <tr>
    <th scope="col">company id</th>
      <th scope="col">name</th>
      <th scope="col">email</th>
      <th scope="col">specialization</th>
      <th scope="col">photo</th>
      <th scope="col">phone</th>
      <th scope="col">founded in</th>
    </tr>
  </thead>
  <tbody>
  {data.map((obj,index)=>{
return (
    <tr key={index}>
    <th scope="row">{obj.id}</th>
    <td>{obj.name}</td>
    <td>{obj.email}</td>
<td>{obj.specialization}</td>
<td>{obj.photo}</td>
<td>{obj.phone}</td>
<td>{obj.founded_in}</td>
  </tr>
    )
})}
  </tbody>
</table></div>


      }

          </div>
          </div>
    )

    }

