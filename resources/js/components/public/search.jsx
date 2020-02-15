import React, {useContext} from 'react';
import Posts from './posts';
import {DataContext} from './store';


const Search = () => {
   const {noResults} = useContext(DataContext);
    return (
        <>
        <Posts />
        {noResults && (
            <div className="col-md-8 mx-auto">
            <p className="text-center">There are no results for your search</p>
         </div>
         )}
        </>
    )
}


export default Search;