import React, {useEffect, useState} from 'react';
import Axios from 'axios';
import {Link} from 'react-router-dom';
import Moment from 'react-moment';



const Home = () => {
  
    const [data, setData] = useState([]);
    const [isLoading, setIsLoading] = useState(false);
    const [isError, setIsError] = useState(undefined);
    useEffect(() => {
      let isCanceled = false; 
     const fetchResults = async() => {
        setIsLoading(true);
        setIsError(false);
         try {
         const  results = await Axios.get('/api/archive/home', {
             headers:{
                 Accept: 'application/json',
                 "Content-Type": 'application/json'
               }
         });
          if(!isCanceled){
           setData(results.data.data);
          }
         } catch (e) {
            if(!isCanceled){
             setIsError(e.message)
            }
            console.log(e)
         }
         setIsLoading(false);
             }
             fetchResults();
     return () => {
       isCanceled = true;
     };
     },[]);
    return (
       <>
          <div className="container">
          <div className="row">
            {
              isLoading ? (
                       <div className="col-md-6 mx-auto text-center py-5">
                       <div className="d-flex justify-content-center">
                     <div className="spinner-border" role="status">
                      <span className="sr-only">Loading...</span>
                     </div>
                     </div>
                       </div>
                   ) :
              data !== undefined && Array.isArray(data) && (
             data.map((item) => {
               const posts = item.posts;
               const Str = item.name.split(" ");
              const category = Str.join("-");
               return(
                 <div className="col-md-12 mx-auto" key={item.id}>
                    <div className="row">
                      <div className="col-md-4 mx-auto text-center">
                          <h5 className="p-1 text-center category-name w3-card">{item.name}</h5>
                      </div>
                    </div>
                    <div className="row home-posts">
                    { posts !== undefined && Array.isArray(posts) && (
                   posts.map((post) => {
                    let title;
                      let shortTitle;
                    const Str = post.post_title.split(" ");
                         const t = Str.join("-");
                         if(t.includes('%')){
                          title = t.replace(/%/g, '-percent');
                         }else{
                           title = t;
                         }
                         if(post.post_title.length > 60){
                          const stripTitle = post.post_title.substr(0, 60);
                           shortTitle = stripTitle.substr(0, stripTitle.lastIndexOf(' '))+'...';
                         }else{
                            shortTitle = post.post_title;
                         }
                       return(
                    <div className="col-md-4 col-lg-3 col-sm-4 mb-2 home-post"  key={post.id}>
                     <div className="card border w3-card home-card">
                    <Link to={`/${post.category.split(" ").join("-")}/${post.id}/${title}`}>
                    <img className="card-img-top img-fluid" src={post.post_img.thumb} alt={post.img_alt}/>
                    </Link>
                   <div className="p-1 ">
                   <Link to={`/${post.category.split(" ").join("-")}/${post.id}/${title}`}>
                   <h6 className="title">{shortTitle}</h6>
                   <span className="date">
                    <i className="far fa-clock"></i>  <Moment fromNow>{post.updated_at}</Moment>
                    </span>
                   </Link>
                   </div>
                 </div>
                 </div>  
                       )
                   })
                   )}
                    </div>
                    <div className="row">
                    <div className="col text-right pt-1">
                      <Link to={`/categories/${item.id}/${category}`} className="text-right read-more">
                      more...
                      </Link>
                    </div>
                </div>
                 </div>
               )
             })
            )}
                   {isError && (
                     <div className="col-md-6 mx-auto text-center">Something went wrong...</div>
                   )}
          </div>
                
          </div>
       </>
    )
}

export default Home;