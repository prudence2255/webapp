import React, {useEffect, useContext, useState} from 'react';
import {withRouter} from 'react-router-dom';
import {DataContext} from './store';
import Axios from 'axios';


const Navbar = ({history}) => {
    const {isOpen, setIsOpen, setIsError, query, setQuery, setUrl} = useContext(DataContext);
    const [categories, setCategories] = useState([]);
    window.addEventListener('resize', function(e){
     if(e.target.innerWidth > 768 && isOpen){
       setIsOpen(false);
     };
    })
    const search = (e) => {
      e.preventDefault();
      setUrl(`/api/archive/search?query=${query}`);
      history.push('/posts/search');  
    }
  const navigate = (id, name) => {
    history.push(`/categories/${id}/${name}`);
    setIsOpen(false);
  }
  useEffect(() => {
    let isCanceled = false;
    const fetchCategories = async() => {
      try {
       const  results = await Axios.get('/api/archive/categories',{
         headers:{
           Accept: 'application/json',
           "Content-Type": 'application/json'
         }
       });
       if(!isCanceled){
        setCategories(results.data.data);
       }
      } catch (e) {
         if(!isCanceled){
          setIsError(e.message)
         }
      }
          }
   fetchCategories();
    return () => {
    isCanceled = true;
    };
  },[])

   return( 
   <>
   <div>
      <ul className={`navigation navbar ${isOpen ? 'open': ''}`}>
        {categories !== undefined && Array.isArray(categories) && categories.map((category) => {
              const Str = category.name.split(" ");
              const title = Str.join("-");
            return(
              <li className="nav-item category" key={category.id}>
             <button className="nav-link text-white text-uppercase btn" 
             onClick={() => navigate(category.id, title)}>
             {category.name}
             </button>  
           </li>
            )
          }) 
          }
          <li className="search-li">
          <form className="search-form">
         <div className="input-group">
         <input type="text" className="form-control search-box" placeholder="Search"
            value={query}
            onChange={e => setQuery(e.target.value)}
            required
         />
        <div className="input-group-append">
         <button className="btn btn-outline-primary search-btn " type="submit"
         onClick={search} disabled={query === ''? true : false}
         ><i className="fa fa-search"></i></button>
         </div>
          </div>
            </form>
          </li>
      </ul> 
   </div>
  </>
)
return(
  <>
  </>
)
   }


export default withRouter(Navbar)