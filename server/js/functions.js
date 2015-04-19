var XHR_TIMEOUT = 2000;
var UP_DIR = './uploads/';

function iloaded() {
  document.getElementById('form-sender').onload = null;
  console.log($('#form-sender').contents().find("body").html());
}
function submit_form(form) {
  document.getElementById('form-sender').onload = iloaded;
  return true;
}
function resize() {
  document.getElementById('sigma_container').style.height = $(window).height()-100+'px';
}

function emptyForm(form) {
  for(var i = 0; i < form.length; ++i) {
    form[i].value = '';
  }
}

function sidebar(action, type) {
  emptyForm(form);
  document.getElementById('chooser').className = action;
  if(type) {
    document.getElementById('form').className = type;
  }
}

function view(node) {
  get(backend+'?id_node='+current_node, function(json) {
    nodes = JSON.parse(json);
    s.graph.clear();
    draw(s, nodes);
  });
}

function edit(node) {
  var form = document.getElementById('form');
  for(var i = 0; i < nodes.length; ++i) {
    if(nodes[i].id_node == node) {
      node = nodes[i];
      break;
    }
  }
  if(node.id_node) {
    if(node.email) {
      sidebar('form', 'profile');
      form.email.value = node.email;
    } else if(node.link) {
      sidebar('form', 'link');
      form.link.value = node.link;
    } else if(node.path) {
      sidebar('form', 'file');
      // make preview
      
    } else if(node.latitude && node.longitude) {
      sidebar('form', 'gps');
      form.latitude.value = node.latitude;
      form.longitude.value = node.longitude;
    } else {
      sidebar('form', 'data');
    }
    form.id_node.value = node.id_node;
    form.name.value = node.name;
    form.description.value = node.description;
  }
}

function sideview(node) {
  var preview = document.getElementById('preview');
  /* already a node
  for(var i = 0; i < nodes.length; ++i) {
    if(nodes[i].id_node == node) {
      node = nodes[i];
      break;
    }
  }
  //*/
  if(node.id_node) {
    preview.innerHTML = '';
    sidebar('preview');
    
    add_viewed_data(preview, 'ID', node.id_node);
    add_viewed_data(preview, 'Name', node.name);
    add_viewed_data(preview, 'Description', node.description);
    if(node.email) {
      add_viewed_data(preview, 'Email', node.email);
    } else if(node.link) {
      add_viewed_data(preview, 'Link', '<a href="'+node.link+'">'+node.link+'</a>');
    } else if(node.path) {
      // make preview
      add_viewed_data(preview, 'Link', '<a href="'+UP_DIR+node.path+'">'+UP_DIR+node.path+'</a>');
    } else if(node.latitude && node.longitude) {
      sidebar('form', 'gps');
      add_viewed_data(preview, 'Latitude', node.latitude);
      add_viewed_data(preview, 'Longitude', node.longitude);
    }
  } else {
    console.log('no node !');
  }
}
function sidesearch(term) {
  get(backend+'?search='+encodeURIComponent(term), function(json) {
    nodes = JSON.parse(json);
    var preview = document.getElementById('preview');
    preview.innerHTML = '';
    sidebar('preview');
    
    for(var i = 0; i < nodes.length; ++i) {
      add_searched_data(preview, nodes[i]);
    }
  });
}
function add_viewed_data(container, name, data) {
  var fieldset = document.createElement('fieldset');
  var legend = document.createElement('legend');
  legend.textContent = name;
  var div = document.createElement('div');
  div.innerHTML = data;
  
  fieldset.appendChild(legend);
  fieldset.appendChild(div);
  container.appendChild(fieldset);
}
function add_searched_data(container, node) {
  var div = document.createElement('div');
  div.className = 'search-result';
  div.textContent = node.name;
  container.appendChild(div);
  div.ondblclick = (function(id) {
    view(id);
    console.log('Clicked '+id);
  })(node.id_node);
}

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
	xhr.timeout = XHR_TIMEOUT;
	xhr.open('GET', url);
	xhr.send();
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

function draw(sigma, nodes) {
  sideview(nodes[0]);
  sigma.graph.addNode({
    // Main attributes:
    id: nodes[0]['id_node'].toString(),
    label: nodes[0]['name'],
    // Display attributes:
    x: 0,
    y: 0,
    size: 2,
    //color: '#f00'
  });
  
  var prev = [], next = [];
  nodes.forEach(function(node) {
    if(node['alpha'] == nodes[0]['id_node'].toString()) {
      next.push(node);
    } else if(node['beta'] == nodes[0]['id_node'].toString()) {
      prev.push(node);
    }
  });
  
  for(var i = 0; i < next.length; ++i) {
    sigma.graph.addNode({
      // Main attributes:
      id: next[i]['id_node'].toString(),
      label: next[i]['name'],
      // Display attributes:
      x: 1,
      y: -(next.length/2)+i+1,
      size: 1,
      color: '#f00'
    }).addEdge({
      id: nodes[0]['id_node'].toString()+'_'+next[i]['id_node'],
      label: next[i]['relation'],
      // Reference extremities:
      source: nodes[0]['id_node'].toString(),
      target: next[i]['id_node'].toString()
    });
  }
  
  for(var i = 0; i < prev.length; ++i) {
    sigma.graph.addNode({
      // Main attributes:
      id: prev[i]['id_node'].toString(),
      label: prev[i]['name'],
      // Display attributes:
      x: -1,
      y: -(prev.length/2)+i+1,
      size: 1,
      color: '#f00'
    }).addEdge({
      id: prev[i]['id_node']+'_'+nodes[0]['id_node'].toString(),
      label: prev[i]['relation'],
      // Reference extremities:
      source: prev[i]['id_node'].toString(),
      target: nodes[0]['id_node'].toString()
    });
  }
  
  sigma.refresh();
}
