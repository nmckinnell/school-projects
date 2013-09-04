/* Simple API to log message into a new div (id="log_console") as an
   alternative to a bunch of alert messages. 

The logger creates the div if there isn't one, so all you need to know
is the log() function, which looks just like alert() or
document.write(). */

/* The following creates an object called SDA.  I'll put all my functions
 * in that object, to avoid collisions with other JS functions.  Thus,
 * it's a personal namespace. */
    
if( SDA == null || typeof(SDA) != "object" ) {
    var SDA = new Object();
}

SDA.createLogConsoleIfNecessary = 
    function () {
        var log_elt = document.getElementById('log_console');
        if( log_elt == null ) {
            log_elt = document.createElement('div');
            log_elt.id = 'log_console';
            document.body.insertBefore(log_elt,document.body.firstChild);
            SDA.log_elt = log_elt;
        }
    }

// window.addEventListener('load',createLogConsoleIfNecessary,false);

SDA.log = function (str) {
    // alert('logging '+str);
    SDA.createLogConsoleIfNecessary();
    // SDA.log_elt.innerHTML += str;
    SDA.log_elt.appendChild(document.createTextNode(str));
}

SDA.logln = function (str) {
    SDA.createLogConsoleIfNecessary();
    SDA.log(str);
    SDA.log_elt.appendChild(document.createElement("br"));
    // SDA.log(str+"<br>\n");
}

SDA.performance_test = function (count) {
    var before = (new Date()).getTime();
    for( var i=0; i<count; i++ ) {
        SDA.logln(i);
    }
    var after = (new Date()).getTime();
    console.log("Appending "+count+" lines took "+(after-before)+"ms");
}
