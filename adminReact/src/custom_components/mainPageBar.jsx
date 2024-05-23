import { useState } from "react"
import Offers_orders from "./offers_orders";
export default function mainPageBar(){

    const [SearchAbout , SetSearchAbout]=  useState('Offers');
    const [sortBy ,setSortBy]= useState(false)
    const [More ,setMore]= useState(false)

    const OrderSubmit = ()=>{
    SetSearchAbout('Orders')
    setSortBy(false)
                }
    const OfferSubmit = ()=>{
        SetSearchAbout('Offers')
        }
    const rateChange =()=>{
        if (sortBy!='by_rate') {
            setSortBy('by_rate')
        }else{
            setSortBy(false)
        }
    }
    const MoreChange = ()=>{
        if (More!='more') {
            setMore('more')
        }else{
            setMore(false)
        }
    }
    const  acceptedClick =()=>{
        setSortBy('accepted')
    }
    const rejectedClick =()=>{
        setSortBy('rejected')
    }
    const  pendingClick =()=>{
        setSortBy('pending')
    }








    return (
<div className="navbarMain">
<nav className="navbar navbar-expand-lg navbar-light bg-light">
  <div className="container-fluid">
    <a className="navbar-brand" href="#">Main Page</a>

    <div className=" navbar" id="navbarSupportedContent">
      <ul className="navbar-nav me-auto mb-2 mb-lg-0">
      <li className="radio_div">
        <div >
  <div className="form-check">
    {/* i change the name that render to the user beacause of what happend on
    offer order component
    if the offer pressed the searchAbout variable will set as Orders
    if the Order pressed the searchAbout variable will set as offers
    the setSearchAbout useState function dont call niether after the request rendering
    */}
  <input onChange = {OrderSubmit} className="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1"/>
  <label className="form-check-label" htmlFor="flexRadioDefault1">
    Orders
  </label>
</div>
<div className="form-check">
  <input  onChange = {OfferSubmit} className="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" defaultChecked/>
  <label className="form-check-label" htmlFor="flexRadioDefault2">
 Offers
  </label>
</div>
</div>
        </li>

        <li className="nav-item">
          <a onClick={MoreChange}className="nav-link " aria-current="page" href="#">{More?'show less':'show more'}</a>
        </li>
        {SearchAbout=='Offers'?<div>
            <li className="nav-item">
          <a onClick={rateChange}className="nav-link " aria-current="page" href="#">{sortBy?'Remove rating':'show Rating'}</a>
        </li>

        </div>:
        <li className="nav-item dropdown">
        <a className="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          filter
        </a>
        <ul  className="dropdown-menu" aria-labelledby="navbarDropdown">
          <li onClick={acceptedClick}><a className="dropdown-item" href="#">accepted</a></li>
          <li><hr className="dropdown-divider"/></li>
          <li onClick={rejectedClick}><a className="dropdown-item" href="#">rejected</a></li>
          <li><hr className="dropdown-divider"/></li>
          <li onClick={pendingClick}><a className="dropdown-item" href="#">pending</a></li>
        </ul>
      </li>}
      </ul>


    </div>
  </div>
</nav>

<Offers_orders searchAbout={SearchAbout} sort_by={sortBy} More={More} />
    </div>

    )

    }


