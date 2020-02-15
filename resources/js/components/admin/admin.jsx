import {useState} from 'react';

const admin = () => {
const [nav, setNav] = useState(undefined);
const [userName, setUserName] = useState(false);
const toggleNav = () => {
    setNav(!nav)
}

return {
    nav, setNav,
    toggleNav,
    userName, setUserName,
}
}











export default admin