import React, {useContext} from 'react';
import Navbar from './navbar';
import {DataContext} from './store';
import {Link, withRouter} from  'react-router-dom';
const Header = ({history}) => {
    const {isOpen, setIsOpen, assets} = useContext(DataContext);
    const toggleNavbar = (e) => {
        e.preventDefault();
        setIsOpen(!isOpen);
    }

    return (
     <>
        <div className="header w3-card">
        <div className="bread clearfix container-fluid">
         <button className="float-left navbar-toggle btn" onClick={toggleNavbar}>
           <i className={`fa ${isOpen ? 'fa-times': 'fa-bars'} fa-2x `}></i>
          </button>
          <button className="btn fav-icon"><Link to="/">
            <img src={`${assets}/fav-icon1.png`} className="img-fluid"/>
          </Link> </button>
        </div>
        </div>
      <Navbar />
     </>
    )
}
export default withRouter(Header);