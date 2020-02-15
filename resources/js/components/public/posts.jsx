import React, {useContext} from 'react';
import {DataContext} from './store';
import {Link,} from 'react-router-dom';
import { useInView } from 'react-intersection-observer';
import Moment from 'react-moment';

const Posts = () => {
  const [ref, inView, entry] = useInView({
                               threshold: 1,
                           })
   const {results, isError, isLoading,
           loadMore,
          } = useContext(DataContext);

  inView && loadMore()
    return(
        <>
        <div className="container-fluid py-3">
                  <div className="row">
                  <div className="col-md-10 mx-auto">
                     <div className="row posts-box" >                  
                   {
                     results !== undefined &&  Array.isArray(results) && (
                    results.map((post, index) => {
                      let title;
                      let shortTitle;
                    const Str = post.post_title.split(" ");
                         const t = Str.join("-");
                         if(t.includes('%')){
                          title = t.replace(/%/g,'-percent');
                         }else{
                           title = t;
                         }
                         if(post.post_title.length > 60){
                          const stripTitle = post.post_title.substr(0, 60);
                           shortTitle = stripTitle.substr(0, stripTitle.lastIndexOf(' '))+'...';
                         }else{
                            shortTitle = post.post_title;
                         }
                    if(results.length === index + 1) {
                      return(
                    <div className="col-md-4 col-lg-3 col-sm-4 mb-2 post-box"  key={post.id} ref={ref}>
                     <div className="card border w3-card posts-card">
                    <Link to={`/${post.category.split(" ").join("-")}/${post.id}/${title}`}>
                    <img className="card-img-top img-fluid " src={post.post_img.thumb} alt={post.img_alt}/>
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
                    }else {
                      return(
                    <div className="col-md-4 col-lg-3 col-sm-4 mb-2 post-box"  key={post.id}>
                     <div className="card border w3-card posts-card">
                    <Link to={`/${post.category.split(" ").join("-")}/${post.id}/${title}`}>
                    <img className="card-img-top img-fluid" src={post.post_img.thumb} alt={post.img_alt}/>
                    </Link>
                   <div className="p-1 ">
                   <Link to={`/${post.category.split(" ").join("-")}/${post.id}/${title}`}>
                   <h6 className="title">{shortTitle}</h6>
                   <span className="date">
                    <i className="far fa-clock"></i> <Moment fromNow>{post.updated_at}</Moment>
                    </span>
                   </Link>
                   </div>
                  </div>
                   </div>  
                       )
                    }
                   })
                   )}
                   {
                    isLoading && (
                      <div className="col-md-12 mx-auto">
                        <div className="row">
                        <div className="col-md-6 mx-auto text-center py-5">
                       <div className="d-flex justify-content-center">
                       <div className="spinner-border" role="status"></div>
                      </div>
                       </div>
                        </div>
                      </div>
                   ) 
                   }
                   {isError && (
                     <div className="col-md-6 mx-auto text-center">Something went wrong...</div>
                   )}
                  </div>
                  </div>
                </div> 
           </div>
        </>
    )

}
export default Posts;