import { useState } from "react";
import axiosClient from "../axios_client"
import LoadingComponent from "./loadingCompnent";
import RatingComponent from "./RatingComponent";

export default  function offers_orders(probs){
    var getRequestUrl
    const [data , setData] = useState([]);
    const [errors , setErrors] = useState([]);
    const [searchAbout , SetSearchAbout] = useState('Orders');
    const [sortBy , setSortBy] = useState(true);
    const [number , setNumber] = useState(0);
    const [Endednumber , setNumberEnded] = useState(0);
    const [Endeddata , setDataEnded] = useState([]);
    const [isLoading , setIsLoading] = useState(true);
    const [offer_id , setOfferId] = useState(false);
    const [company_id , setCompanyId] = useState(false);
if (searchAbout!=probs.searchAbout || sortBy!=probs.sort_by){
    console.log('the search ot equal probs');
    //we change the names down because when we sent the request
    //searchAbout is the previos value but then when we render the return
    //searchAbout is the new value

    SetSearchAbout(probs.searchAbout)
    setSortBy(probs.sort_by)
    debugger
    if (probs.sort_by) {
    getRequestUrl = `get_offers?search_about=${probs.searchAbout}&sort_by=${probs.sort_by}&company_id=${company_id}&offer_id=${offer_id}`
    debugger}else{
        getRequestUrl = `get_offers?search_about=${probs.searchAbout}&sort_by=&company_id=${company_id}&offer_id=${offer_id}`

    debugger}

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
        //if the response is for orders
        if (response.data.data) {
            console.log(response);
            setData(response.data.data)
            console.log(response.data.data);
            setNumber(response.data.number)
            console.log(response.data.number);

        }
        //if it for offers
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



    return (
      isLoading?<LoadingComponent/>:   <div>


      {errors.map((e,index)=>{
      return (
          <div key={index} className="alert alert-danger alert_div" role="alert"   >
            {e}
          </div>
          )
      })}
      {probs.searchAbout!='Offers'?

      // render the Orders because searchAbout in url is Orders
      <div>
      <h5>
                  {`the number of orders is ${number}`}
              </h5>
      <table className="table table-striped">

        <thead>
          <tr>
          <th scope="col">order id</th>
            <th scope="col">user_id</th>
            <th scope="col">company_id</th>
            <th scope="col">the_job</th>
            <th scope="col">user_cv</th>
            <th scope="col">status</th>
            <th scope="col">company_report</th>
            <th scope="col">offer_id</th>
            {probs.More?
            <th scope="col">created_at</th>
            :null}
            {probs.More?
            <th scope="col">updated_at</th>
           :null}
          </tr>
        </thead>
        <tbody>
        {data.map((obj,index)=>{
      return (
          <tr key={index}>
          <th scope="row">{obj.id}</th>
          <td>{obj.user_id}</td>
          <td>{obj.company_id}</td>
      <td>{obj.the_job}</td>
      <td>{obj.user_cv}</td>
      <td>{obj.status}</td>
      <td>{obj.company_report}</td>
      <td>{obj.offer_id}</td>
      {probs.More?
        <td>{`in : ${obj.created_at.split('T')[0]} at : ${obj.created_at.split('T')[1].split('.')[0]}`}</td>
        :null}
            {probs.More?
      <td>{`in : ${obj.updated_at.split('T')[0]} at : ${obj.updated_at.split('T')[1].split('.')[0]}`}</td>
            :null}

        </tr>
          )
      })}
        </tbody>
      </table></div> :
      // render the Offers because searchAbout in url is offers
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
            {probs.sort_by?<th scope="col">the rate</th>:null}
            {probs.More?
            <th scope="col">offer end at</th>
            :null}
            {probs.More?
            <th scope="col">is affective</th>
           :null}

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
      <td>{obj.official_holidays==1?"true":"false"}</td>
      <td>{obj.period}</td>
      <td>{obj.salary}</td>
      <td>{obj.specialization_wanted}</td>
      <td>{obj.the_days}</td>
      <td>{obj.the_job}</td>
      {/* ?////////////////////////////////////////////////////////////// */}
      {probs.sort_by?<RatingComponent star_rate={obj.rating} />:null}
      {probs.More?
        <td>{obj.offer_end_at}</td>
        :null}
            {probs.More?
        <td>{'now affective'}</td>
            :null}


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
            {probs.sort_by?<th scope="col">the rate</th>:null}
            {probs.More?
            <th scope="col">offer end at</th>
            :null}
            {probs.More?
            <th scope="col">is affective</th>
           :null}

          </tr>
        </thead>
        <tbody>
        {Endeddata.map((obj,index)=>{
      return (
          <tr key={index}>
          <th scope="row">{obj.id}</th>
          <td>{obj.company_id}</td>
          <td>{obj.hour_begin}</td>
      <td>{obj.official_holidays==1?"true":"false"}</td>
      <td>{obj.period}</td>
      <td>{obj.salary}</td>
      <td>{obj.specialization_wanted}</td>
      <td>{obj.the_days}</td>
      <td>{obj.the_job}</td>
      {/* //////////////////////////////////////////////////////////////////////// */}
      {probs.sort_by?<RatingComponent star_rate={obj.rating} />:null}
      {probs.More?
        <td>{obj.offer_end_at}</td>
        :null}
            {probs.More?
        <td>{'not affective'}</td>
            :null}
        </tr>
          )
      })}
        </tbody>
      </table>


      </div>
      }

          </div>

    )

    }
