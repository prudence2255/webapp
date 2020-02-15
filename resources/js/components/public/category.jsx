import React, {useContext} from 'react';
import {DataContext} from './store';
import {withRouter} from 'react-router-dom';
import Posts from './posts';


const Category = (props) => {
            const {id} = props.match.params;
    const {setUrl, noResults} = useContext(DataContext);
             setUrl(`/api/archive/categories/${id}`);
    return (
        <>
         <Posts />
         {noResults && (
            <div className="col-md-8 mx-auto">
            <p className="text-center">There are no posts in this category</p>
         </div>
         )}
        </>
    )
}

export default withRouter(Category);