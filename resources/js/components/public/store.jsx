import React, { useState, useEffect} from "react";
import admin from '../admin/admin';
import postRequest from '../admin/PostRequests';
import Axios from 'axios';
export const DataContext = React.createContext({});

  const store = (props) => {
  const adminPanel = admin();
  const postRequests = postRequest(); 
  const [results, setResults] = useState([]);
  const [isError, setIsError] = useState(undefined);
  const [isLoading, setIsLoading] = useState(false);
  const [isOpen, setIsOpen] = useState(undefined);
  const [url, setUrl] = useState(``);
  const [activePage, setActivePage] = useState(1)
  const [query, setQuery] = useState('');
  const [noResults, setNoResults] = useState(false);
  const [lastPage, setLastPage] = useState(3);
  const [post, setPost] = useState({});
  const [isPostError, setIsPostError] = useState(false);
  const [postUrl, setPostUrl] = useState('');
  const [isPostLoading, setIsPostLoading] = useState(false);
  const [hasMore, setHasMore] = useState(true);
 const [assets, setAssets] = useState('');
  let isPage = url.includes('?page');

//headers

  let options = {
    headers:{
      Accept: 'application/json',
     "Content-Type": 'application/json'
      }
  }
  //useEffect for front end get requests
  useEffect(() => {
    let isCanceled = false; 
   const fetchResults = async() => {
   setNoResults(false);
    setIsLoading(true);
    setIsError(false);
       try {
       const  results = await Axios.get(url, options);
        if(!isCanceled){
            setResults([...results.data.data]);
            if(results.data.data.length === 0){
              setNoResults(true)
            }
           }
       
       } catch (e) {
          if(!isCanceled){
           setIsError(e.message)
          }     
       }
       setIsLoading(false);
      }
           fetchResults();
   return () => {
     isCanceled = true;
   };
   },[!isPage && url, postUrl]);

   //useEffect for recent posts
   useEffect(() => {
    let isCanceled = false; 
    const fetchResults = async() => {
      setIsPostLoading(true);
      setIsPostError(false);
         try {
         const  results = await Axios.get(postUrl, options);
          if(!isCanceled){
              setPost(results.data.data);
             
             }
         
         } catch (e) {
            if(!isCanceled){
             setIsPostError(e.message)
            }     
         }
         setIsPostLoading(false);
        }
             fetchResults();
     return () => {
       isCanceled = true;
     };
     },[postUrl]);
     
     //useEffect for resetting results, hasMore, activePage and lastPage
   useEffect(() => {
    let isCanceled = false;
    if(!isCanceled){
     if(!isPage){
     setResults([]);
      setHasMore(true);
      setActivePage(2);
      setLastPage(3)
     }      
    }
     return () => {
      isCanceled = true;
     };
   }, [!isPage && url, postUrl]);

   useEffect(() => {
    if(document.getElementById('app')){
      const assets = document.getElementById('app').getAttribute('assets');
       setAssets(assets);
     }
     return () => {
     
     };
   }, [])

   //loadMore function
  const loadMore = () => {
    if(!hasMore) return
    if(isLoading) return;
   handleLoadMore();
  }
  const handleLoadMore = async() => {
          setActivePage(activePage + 1);
           setIsLoading(true);
           setIsError(false);
           try {
          const  result = await Axios.get(`${url}?page=${activePage}`, options);
          setResults([...results, ...result.data.data]);
          setLastPage(result.data.meta.last_page);
          if(activePage === lastPage || results.length < 8){
            setHasMore(false);
          }       
        } catch (e) {
        setIsError(e.message)
       }
      setIsLoading(false);
     }           
     
    const value = {
         results, setResults,
          isLoading, setIsLoading,
          isError, setIsError,
         url, setUrl,
         isOpen, setIsOpen,
         activePage, 
         query, setQuery,
         noResults, loadMore,
         post, setPostUrl,isPostError,
          isPostLoading, assets,
    };
   
  return(
    <DataContext.Provider value={{...value,...adminPanel,...postRequests}}>
    {props.children}
    </DataContext.Provider>
  )
  
}
 

export default store