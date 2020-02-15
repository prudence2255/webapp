import React, {useContext,} from 'react';
import {useParams} from 'react-router-dom';
import {DataContext} from './store';
import Posts from './posts';
import {Helmet} from "react-helmet";
import MetaTags from 'react-meta-tags';
import {FacebookShareButton,
        TwitterShareButton,
        WhatsappShareButton} from 'react-share';


const Post = () => {
    const params = useParams();
    const {id} = params;
    const { post, setPostUrl,
            isPostError, isPostLoading,
            results, setUrl} = useContext(DataContext);
    setPostUrl(`/api/archive/posts/${id}`); 
    setUrl(`/api/archive/posts`)

    return(
        <>
             {typeof post === 'object' && post !== undefined && (
                <Helmet>
                 <title>{post.post_title}</title>
                 <meta property="og:type" content="article" />
                 <meta name="description" lang="en" content={post.description} />
                 <meta name="twitter:card" content="image" />
                 <meta name="twitter:site" content="@enthusiastgh" />
                 <meta name="twitter:title" content={post.post_title} />
                 <meta name="twitter:url" content={window.location.href} />
                 <meta name="twitter:description" content={post.description} />
                  <meta name="twitter:image" content={post.post_img && post.post_img.thumb} />
                 <meta property="og:title" content={post.post_title} />
                 <meta property="og:image" content={post.post_img && post.post_img.thumb} />
                 <meta property="og:url" content={window.location.href} />
                 <meta property="og:description" content={post.description}/>
                <link rel="canonical" href={window.location.href} />
                </Helmet>
             )}
            
           <div className="container-fluid">
               {isPostLoading ? (
                <div className="col-md-6 mx-auto text-center">
                Loading...
                </div>
               ) : typeof post === 'object' && post !== undefined && (
               <div>
               <span className="mx-1 date">
                   <span className="w3-left">{post.category}</span>
                   <span className="w3-right">{new Date(post.updated_at).toDateString()}</span>
               </span>
               <div className="row">
                   <div className="col-md-7 mx-auto">
                       <h3 className="post-title mx-2">{post.post_title}</h3>
                   </div>
                 </div>
                <div className="row">
                   <div className="col-md-7 mx-auto">
                   <div className="card text-center mb-3 "> 
                       <img className="card-img-top img-fluid img-card" src={post.post_img && post.post_img.image} alt={post.img_alt}/>
                    </div>
                   </div>
                </div> 
                <div className="row">
                    <div className="col-md-6 mx-auto text-center">
                      <div className="img-alt">{post.img_alt}</div>
                    </div>
                </div>
                <div className="row">
                    <div className="col-md-7 mx-auto py-3">
                    <div dangerouslySetInnerHTML={{__html: post.post_content}}/>
                    </div>
                </div>
                <div className="row">
                    <p className="col-md-7 mx-auto"> <strong>Source: </strong> 
                    <a href={`https://${
                                    post.source && post.source.substr(post.source.indexOf(":") + 2, )}`
                        } className="w3-text-blue" target="_blank">{
                            post.source && post.source.substr(post.source.indexOf(":") + 2, )
                            }
                    </a>
                    </p>
                </div>
                <div className="row">
                    <div className="col-md-6 mx-auto mb-2 text-center">
                       <FacebookShareButton url={window.location.href} className="facebook">
                       <i className="fab fa-facebook-square fa-2x"></i>
                       </FacebookShareButton>
                        <WhatsappShareButton url={window.location.href} className="whatsapp">
                        <i className="fab fa-whatsapp-square fa-2x"></i>
                        </WhatsappShareButton>
                        <TwitterShareButton url={window.location.href} className="twitter">
                        <i className="fab fa-twitter-square fa-2x"></i>
                        </TwitterShareButton>
                    </div>
                </div>
               </div>
               )}
               {isPostError && (
                     <div className="col-md-6 mx-auto text-center">Something went wrong...</div>
                   )}  
            </div>
            <div className="row">
           <div className="col-md-6 mx-auto">
               {Array.isArray(results) && results.length > 0 && (
                <p className="recent-posts text-center">
                        Trending stories
                </p>
               )}
           </div>     
        </div>
        <Posts />   
        </>
    )
}


export default Post;