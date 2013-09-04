/* Functions to get and set cookies, based on code from 
   http://www.w3schools.com/js/js_cookies.asp and
   http://www.elated.com/articles/javascript-and-cookies/

   Scott D. Anderson
   Fall 2009
*/


/* This function based on http://www.w3schools.com/js/js_cookies.asp.

Takes the name and value of the cookie (both strings) and the number of
days for the cookie to last (an integer, presumably).  expire_days is
optional; you can omit it and get a cookie that expires when the browser
closes.  Path is a pathname for a set of pages that should process this
cookie.  You might set it to "/" if you want every page in your domain
(e.g. cs.wellesley.edu) to match, or /~youraccount/ to restrict the cookie
to just your account.

Example: The following code sets the cookie named "zip_code" to a value
gotten from the user, default value of 02481.  It will expire in a week.

var zip = prompt("What is your zip code","02481");
setCookie("zip_code",zip,7);

*/

function setCookie(cookie_name,value,expire_days,path) {
    // This "if" avoids user errors where they pass in an undefined
    // variable as the name of the cookie
    if( typeof(cookie_name) == "undefined") {
        alert("cookie name is not defined");
        return;
    }
    // Similarly, this avoids user errors where they pass in an undefined
    // variable as the name of the cookie
    if( typeof(value) == "undefined") {
        alert("value is not defined");
        return;
    }
    // The "escape()" function encodes characters that would be
    // problematic in a cookie value, such as spaces, semi-colons and
    // other punctuation, etc.  We don't encode the cookie name because
    // we're assuming it's a simple string.  We should probably check...
    var cookie_string = cookie_name+"="+escape(value);
    // now, add on the expiration date, if any
    if( expire_days != null ) {
        var expiration_date = new Date();
        expiration_date.setDate(expiration_date.getDate()+expire_days);
        cookie_string = cookie_string + ";expires="+expiration_date.toGMTString();
    }
    // add on the path, if any
    if( path != null ) {
        cookie_string = cookie_string + "path="+path
    }
    // Finally, set the cookie.
    document.cookie = cookie_string;
}


/* This function also based on http://www.w3schools.com/js/js_cookies.asp
Takes a cookie name (a string) and returns its value, also a string.
If there is no such cookie, returns the empty string.

Example: The following code gets the cookie named "zip_code," if any.

var zip = getCookie("zip_code");

*/

function getCookie(cookie_name) {
    // first, see if there is a cookie at all
    if (document.cookie.length == 0) {
        return "";
    }
    // The indexOf method looks for the name of the cookie and the "="
    // that separates the cookie's name from the cookie's value.  It
    // returns the location, if it finds it, otherwise -1, so -1 means no
    // cookie was found.
    cookie_name_start_index = document.cookie.indexOf(cookie_name+'=');
    if (cookie_name_start_index == -1) {
        return "";
    }
    // The cookie's value starts farther on from the cookie name that we
    // found earlier.
    var cookie_value_start_index = cookie_name_start_index + cookie_name.length+1;
    // The cookie's valie ends with a semi-colon, so look for that to mark
    // the end.
    var cookie_value_end_index = document.cookie.indexOf(";",cookie_value_start_index);
    if( cookie_value_end_index == -1 ) {
        // No semi-colon, so use the end of the string
        cookie_value_end_index = document.cookie.length;
    }
    // The substring method extracts a part of a string.
    var cookie_value = document.cookie.substring(cookie_value_start_index,cookie_value_end_index);
    // in case there are odd characters in the cookie value that were escaped, unescape them
    return unescape(cookie_value);
}
