import{Navigate, createBrowserRouter} from "react-router-dom";

import Login from "./views/login";
import Add_admin from "./views/add_admin";
import Delete_admin from "./views/delete_admin";
import Not_found from "./views/not_found";
import Main_page from "./views/main_page";
import Dashboard from "./views/dashboard";
import DefaultLayout from "./components/DefaultLayout";
import GuestLayout from "./components/GuestLayout";
import SpecializationComponent from "./views/soecializations";
const route =createBrowserRouter([


    {

        path : '/',
        element: <DefaultLayout/>,
        children:[


        {

            path : '/',
            element: <Navigate to="/main_page"/>,

        },

        {

            path : '/main_page',
            element:<Main_page/>

        },

        {

            path : '/add_admin',
            element:<Add_admin/>

        },
        {

            path : '/delete_admin',
            element:<Delete_admin/>

        },
        {

            path : '/dashboard',
            element: <Dashboard/>,

        },
        {

            path : '/specializations',
            element:  <SpecializationComponent/>,

        },


    ]

    },

    {

        path : '/',
        element:<GuestLayout/>,
        children:
        [
            {

                path : '/login',
                element:<Login/>

            },

        ]

    },





    {

        path : '*',
        element:<Not_found/>

    },

]);

export default route;
