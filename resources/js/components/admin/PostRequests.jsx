import {useState, useEffect} from 'react';
import Axios from 'axios';

const postRequest = () => {
   const [response, setResponse] = useState([]);
   const [path, setPath] = useState(``);
   const [user, setUser] = useState({})
   const [isRequestError, setIsRequestError] = useState(false);
   const [category, setCategory] = useState([]);
   const [message, setMessage] = useState('');
   const [errors, setErrors] = useState([]);
   const [isItem, setIsItem] = useState(false);
   const [load, setLoading] = useState(false);
   const [totalPosts, setTotalPosts] = useState(40);
   const [currentPage, setCurrentPage] = useState(1);
  
   //useEffect for get requests for backend
   useEffect(() => {
      let isCanceled = false;
      fetchData({
         method: 'get',
         url: path,
      }).then(res => {
        if(!isCanceled){
         setResponse(res.data.data);
        }
      }).catch(e => {
         if(!isCanceled){
            setIsRequestError(e.message)
         }
      })
      return () => { 
         isCanceled = true;
      };
   }, [path, message])

   //useEffect for resetting success message 
  useEffect(() => {
      setMessage('')
      setIsRequestError(false);
      setErrors([]);
     return () => {
     };
  }, [path])

  //pagination
   const paginate = (pageNumber) => {
      fetchData({
         method: 'get',
         url: `/api/auth/posts?page=${pageNumber}`,
      }).then(res => {
         setTotalPosts(res.data.meta.total);
         setCurrentPage(pageNumber);
         if(Array.isArray(res.data.data)){
            setResponse(res.data.data);
         }
      }).catch(e => {
            setIsRequestError(e.message)
      })
   }

   //promise containing headers and token if logged in
   const fetchData = (options) => {
      const headers = {
               'Accept': 'application/json',
               'Content-Type': 'application/json'
          }
          if(loggedIn()) {
            headers['Authorization'] = `Bearer ${getToken()}`;
        }
        return Axios({
          ...options,
          headers,  
        });
       }

      //get the token from local storage 
       const getToken = () => {
         return localStorage.getItem('token')
      }  

//set the token in local storage
      const setToken = (token) => {
          localStorage.setItem('token', token)
      }
  
      //login method
      const login = (path, value, history, redirect) => { 
            setLoading(true);
            fetchData({
               method: 'post',
               url: path,
               data: value
            }).then(res => {
              if(res.data.status === 'success'){
               setToken(res.data.token);
               history.push(redirect);
              }else if(res.data.status === 'error'){
               setErrors(Object.values(res.data.errors).flat());
              }else if(res.data.message === 'Email or password invalid'){
                 setMessage(res.data.message);
              }
               setLoading(false); 
            })
            .catch(e => {
               setIsRequestError(e.message)
               setLoading(false); 
            })        
     }

     //add or updating method
     const addOrUpdate = (method, path, value, history, redirect) => {
      setLoading(true);
      fetchData({
         method: method,
         url: path,
         data: value
      }).then(res => {
         if(res.data.status === 'success'){
            if(method === 'put'){
               setResponse(res.data.data);
            }else if(method === 'post' && Array.isArray(response)){
               const data = res.data.data;
               setResponse([...response, data]);
            }
            history.push(redirect);
            setMessage(res.data.message);
         }else if(res.data.status === 'error'){
            setErrors(Object.values(res.data.errors).flat());
         }
        
         setLoading(false)
      })
      .catch(e => {
            setIsRequestError(e.message)
            setLoading(false)
      })     
     }

     //fetching new posts from various sources
     const addNewPosts = () => {
        setLoading(true);
        fetchData({
           method: 'get',
           url: '/api/auth/main'
        }).then(res => {
           setMessage(res.data.message);
           setLoading(false);
        }).catch(e => {
           setIsRequestError(e.message)
           setLoading(false);
        })
       
     }
     //change password method
     const resetPassword = (method, path, value, url, history, redirect) => {
      setLoading(true);
      fetchData({
         method: method,
         url: path,
         data: value
      }).then(res => {
         if(res.data.status === 'success'){
            setMessage(res.data.message);
            logout(url, history, redirect)
         }else if(res.data.status === 'error'){
            setErrors(Object.values(res.data.errors).flat());
         }else if(res.data.message === 'The old password does not match'){
            setMessage(res.data.message)
         }
       
        setLoading(false)
      })
      .catch(e => {
            setIsRequestError(e.message)
            setLoading(false)
      })     
     }
     //get a specific item 
     const getItem = (id) => {
        if(Array.isArray(response)){
           return response.find(item => item.id === id);
        }else{
           return response
        }
     }

     //delete and item
     const deleteItem = (path, id, history, redirect) => {
      fetchData({
         method: 'delete',
         url: path,
      }).then(res => {
        if(Array.isArray(response) && response !== undefined){
         if(res.data.message === 'Logged in user or admin cannot be deleted'){
            setResponse([...response]);
         }else{
            const data = response.filter(item => item.id !== id);
            setResponse(data);
            }
        }
         history.push(redirect);
         setMessage(res.data.message);
      })
      .catch(e => {
            setIsRequestError(e.message)
      })     
     }

     //make user an admin
     const makeAdmin = (path, history, redirect, id) => {
      fetchData({
         method: 'put',
         url: path,
      }).then(res => {
       const data = [...response];
       const itemIndex = response.findIndex(item => item === getItem(id));
       const user = data[itemIndex];
         user.role = 'admin';
         setResponse(data);
         history.push(redirect);
         setMessage(res.data.message);
      })
      .catch(e => {
            setIsRequestError(e.message)
      })  
     }

     //publish or unpublish a post
     const publishOrUnpublish = (path, history, redirect) => {
      fetchData({
         method: 'put',
         url: path,
      }).then(res => {
         history.push(redirect);
         setMessage(res.data.message);
      })
      .catch(e => {
            setIsRequestError(e.message)
      })     
     }
     //check wether a user is logged in
     const loggedIn = () => {
        const token = getToken();
        return !!token;
     }
//log a user out
     const logout = (path, history, redirect) => {
        fetchData({
           method: 'get',
           url: path
        }).then(res => {
            setResponse(res.data.message);
            localStorage.removeItem('token');
            history.push(redirect);
        }).catch(e => {
           setIsRequestError(e.message)
        }) 
     }
     

     //reset password when you forget your password
     const forgotPassword = (values, history, redirect) => {
      setLoading(true);
         Axios({
            method: 'post',
            url: '/api/auth/reset',
            data: values,
            headers: {
               'Accept': 'application/json',
               'Content-Type': 'application/json'
            }
         }).then(res => {
            if(res.data.status === 'success'){
               history.push(redirect);
               setMessage(res.data.message);
            }else if(res.data.message === 'This password reset token is invalid.'){
                setMessage(res.data.message);
            }else if(res.data.message === 'We cant find a user with that e-mail address.'){
               setMessage(res.data.message)
            }else if(res.data.status === 'error'){
               setErrors(Object.values(res.data.errors).flat());
            }
            setLoading(false)
         }).catch(e => {
           setIsRequestError(e.message);
           setLoading(false)
         })
     }
     
     //send email for password reset
     const sendMail = (values, history, redirect) => {
      setLoading(true);
      Axios({
         method: 'post',
         url: '/api/auth/forgotPassword',
         data: values,
         headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
         }
      }).then(res => {
         if(res.data.status === 'success'){
            history.push(redirect);
            setMessage(res.data.message);
         }else if(res.data.status === 'error'){
            setMessage(res.data.message)
         }
        
         setLoading(false)
      }).catch(e => {
         setIsRequestError(e.message)
         setLoading(false)
      })
  }
   return {
      response, 
      user, setUser,
      isRequestError, 
      fetchData,loggedIn,
      login, logout,
      setPath,category,
      addOrUpdate, message,
      deleteItem, publishOrUnpublish,
      makeAdmin, isItem, setIsItem,
      resetPassword, setCategory,
      forgotPassword, sendMail, load,
      currentPage, totalPosts,
      paginate, errors,
      addNewPosts,
   }
   
}



 
export default postRequest;