var XHR_TIMEOUT = 2000;

function get(url, callback) {
	var xhr = new XMLHttpRequest();
	xhr.onreadystatechange = function(e) {
		if(xhr.readyState == 4) { //num for IE 7 and 8
			if(xhr.status == 200) {
        callback(xhr.responseText);
			} else {
				callback(null);
			}
		}
	}
	
	xhr.open('GET', url);
	xhr.send();
	
	setTimeout(function(){ xhr.abort(); }, XHR_TIMEOUT);
}

function post(action, x, y, comment) {
  if(typeof(x) === 'undefined' || typeof(y) === 'undefined') {
    x = actual['x'];
    y = actual['y'];
  }
  var post = 'x='+x+'&y='+y;
  
  if(action == 'post' || action == 'comment') {
    var mess = getElmt('mess');
    var text = mess.value.replace(/^\s+/g, '').replace(/\s+$/, '').replace(/\s+/g, ' ');
    mess.value = text;
    if(text.length < MIN_LEN) {
      error(-8, 'Text too short (min ', MIN_LEN, ' characters).', '\n');
      return;
    } else if(text.length > MAX_LEN) {
      error(-9, 'Text too long (max ', MAX_LEN, ' characters).', '\n');
      return;
    }
    post += '&m='+encodeURIComponent(text);
    
    if(action == 'post') {
      post += '&e='+expiration;
      if(getElmt('private').checked) {
        post += '&p=1';
      }
    }
  } else if(action == '+') {
    post += '&l='+comment;
  } else if(action == '-') {
    post += '&l=-'+comment;
  } else if(action == 'report') {
    if(!confirm('Report message at case '+x+'x'+y+' ?')) {
      return;
    }
    post += '&r='+comment;
  }
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function() {
    if(xhr.readyState == 4) { //num for IE 7 and 8
      if(xhr.status == 200) {
        if(xhr.getResponseHeader('Error') === null || xhr.getResponseHeader('Error') === '') {
          reset();
          read(x, y);
          //setTimeout(function() { success(xhr.responseText); }, 100); for IE7 to change DOM before
          var res = 'Successfully '+action+(action[action.length-1]=='e'?'d':'ed')+' case '+x+'x'+y+'.';
          if(action == 'report') {
            alert(res);
          }
          success(xhr.responseText);
        } else {
          error(xhr.getResponseHeader('Error'), xhr.responseText);
        }
        
      } else {
        error(-1, 'Erreur de connexion avec le serveur.');
      }
    }
  }
  
  xhr.open('POST', '/post.php');
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); //default form format
  xhr.send(post);
  
  setTimeout(function(){ xhr.abort(); }, TIMEOUT);
}
