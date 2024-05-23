import { Link } from "react-router-dom";
import { UseStateContext } from "../context/ContextProvider";


export default function Navbar (){
    const user = JSON.parse(localStorage.getItem('User'));
console.log(user);
const handleLogout =()=>{
localStorage.removeItem('ACCESS_TOKEN')
localStorage.removeItem('User')
localStorage.removeItem('Remember')
window.location.reload();
}


    return (

<nav className="navbar navbar-dark bg-dark fixed-top">
  <div className="container-fluid">
    <a className="navbar-brand" href="#">Welcome Admin</a>
    <button className="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar" aria-label="Toggle navigation">
      <span className="navbar-toggler-icon"></span>
    </button>
    <div className="offcanvas offcanvas-end text-bg-dark " tabIndex="-1" id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel">
      <div className="offcanvas-header ">
        <h5 className="offcanvas-title" id="offcanvasDarkNavbarLabel">Choose Bage</h5>
        <button type="button" className="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div className="offcanvas-body">
        <ul className="navbar-nav justify-content-end flex-grow-1 pe-3">
          <li className="nav-item list-btn">
          <button className="btn btn-dark ">
    <Link className="link-light  Links" to="/main_page">Main_page</Link>
    </button>
          </li>
          <li className="nav-item list-btn">
          <button className="btn btn-dark ">
          <Link  className = "link-light Links"to="/dashboard" >Dashboard</Link>
            </button>
          </li>

            { user.id==1 ?
            <div>
            <li className="nav-item list-btn">
          <button className="btn btn-dark">
          <Link  className = "link-light  Links"to="/add_admin">Add Admin</Link>
            </button>
          </li>
          <li className="nav-item list-btn">
          <button className="btn btn-dark">
          <Link  className = "link-light  Links"to="/delete_admin">Delete Admin</Link>
            </button>
          </li>
          </div>
          : null

            }


          <li className="nav-item  login-btnlist">
        <button className="btn btn-danger" onClick={handleLogout}>
        <Link  className = "link-light  Links"to="/login"><b>logout</b></Link>
        </button>
</li>
        </ul>


      </div>
    </div>
  </div>
</nav>
    )

    }




