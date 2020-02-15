import React, {useEffect, useContext} from 'react';
import ReactDOM from 'react-dom';
import Index from './public/index';
import Store from './public/store';
import {BrowserRouter as Router} from 'react-router-dom';
import 'moment-timezone';
import ReactGa from 'react-ga';
import {DataContext} from './public/store';



function App() {
  const {url, postUrl} = useContext(DataContext);
    useEffect(() => {
      ReactGa.initialize('UA-157383685-1');
      ReactGa.pageview(window.location.pathname + window.location.search);
      return () => {
      }
    },[url, postUrl]);
   return (
     <Router>
         <Store>
          <Index />
      </Store> 
     </Router>  
   )
}

export default App;

if (document.getElementById('app')) {
    ReactDOM.render(<App />, document.getElementById('app'));
}
