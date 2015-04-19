<?php
require_once('lib.php');
$login = current_user() ? '' : '<a href="login.php">Login</a>';
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="Graphisy, the easiest way to create Graphs">
    <meta name="author" content="140849,Axtux">
    <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <link rel="icon" href="./favicon.ico">
    
    <title>Graphisy</title>
    
    <link href="./css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/dashboard.css" rel="stylesheet">
  </head>
  <body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <ul class="nav navbar-nav navbar-left">
            <li><img src="./android-icon-48x48.png"></li>
            <li><a class="navbar-brand" href="./">raphisy</a></li>
            <li><a class="navbar-brand" href="#add" onclick="return sidebar('add');"><span class="glyphicon glyphicon-plus" ></span>Add node</a></li>
          </ul>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><?=$login?></li>
            <!--li><a href="./help.html">Help</a></li-->
          </ul>
          <form class="navbar-form navbar-right" onsubmit="return search(this);">
            <input class="form-control" placeholder="Search..." type="text"/>
          </form>
        </div>
      </div>
    </nav>
    
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-6 sidebar">
          <div id="chooser" >
            <div id="add" class="btn-group">
              <button class="btn btn-default" onclick="sidebar('form', 'data');">
                <span class="glyphicon glyphicon-text-size" aria-hidden="true"></span>
                <span class="glyphicon-class">Data</span>
              </button>
              <button class="btn btn-default" onclick="sidebar('form', 'profile');">
                <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                <span class="glyphicon-class">Profile</span>
              </button>
              <button class="btn btn-default" onclick="sidebar('form', 'link');">
                <span class="glyphicon glyphicon-link" aria-hidden="true"></span>
                <span class="glyphicon-class">Link</span>
              </button>
              <button class="btn btn-default" onclick="sidebar('form', 'file');">
                <span class="glyphicon glyphicon-file" aria-hidden="true"></span>
                <span class="glyphicon-class">File</span>
              </button>
              <button class="btn btn-default" onclick="sidebar('form', 'gps');">
                <span class="glyphicon glyphicon-screenshot" aria-hidden="true"></span>
                <span class="glyphicon-class">GPS Coordinates</span>
              </button>
            </div>
            <iframe name="form-sender" id="form-sender" style="display: none;" ></iframe>
            <form id="form" method="post" action="http://deel.tk/mic/api.php" target="form-sender" enctype="multipart/form-data" onsubmit="return submit_form(this);">
              <input type="hidden" name="id_node" >
              <div class="form-group" id="data">
                <label for="recipient-name" class="control-label">Name :</label>
                <input type="text" class="form-control" name="name">
                <label for="message-text" class="control-label">Description :</label>
                <textarea class="form-control" name="description"></textarea>
              </div>
              <div class="form-group" id="profile">
                <label for="message-text" class="control-label">E-mail :</label>
                <input type="text" class="form-control" name="email">
              </div>
              <div class="form-group" id="link">
                <label for="message-text" class="control-label">Link :</label>
                <input type="text" class="form-control" name="link">
              </div>
              <div class="form-group" id="file">
                <label for="message-text" class="control-label">File :</label>
                <input type="file" class="control-label" name="file">
              </div>
              <div id="gps">
                <div class="form-group">
                  <button type="button" class="btn btn-info" onclick="getLocation()">Locate your device</button>
                </div>
                <div class="form-group">
                  <label for="message-text" class="control-label">Latitude :</label>
                  <input type="text" class="form-control" name="latitude" id="lat">
                </div>
                <div class="form-group">
                  <label for="message-text" class="control-label">Longitude :</label>
                  <input type="text" class="form-control" name="longitude" id="lon">
                </div>
              </div>
              <div id="edge">
                <div class="form-group">
                  <label for="message-text" class="control-label">Source :</label>
                  <input type="text" class="form-control" name="alpha">
                </div>
                <div class="form-group">
                  <label for="message-text" class="control-label">Target :</label>
                  <input type="text" class="form-control" name="beta">
                </div>
                <div class="form-group">
                  <label for="message-text" class="control-label">Relation :</label>
                  <input type="text" class="form-control" name="relation">
                </div>
              </div>
              <button type="submit" class="btn btn-primary" >Create/Update</button>
              <br><br>
            </form>
            <div id="preview">
              <div class="form-group" id="preview-data">
                <fieldset>
                  <legend>Personalia:</legend>
                  <div id="preview-name">
                </fieldset>
                <label for="recipient-name" class="control-label">Name :</label>
                <input type="text" class="form-control" name="name">
                <label for="message-text" class="control-label">Description :</label>
                <textarea class="form-control" name="description"></textarea>
              </div>
              <div class="form-group" id="preview-profile">
                <label for="message-text" class="control-label">E-mail :</label>
                <input type="text" class="form-control" name="email">
              </div>
              <div class="form-group" id="preview-link">
                <label for="message-text" class="control-label">Link :</label>
                <input type="text" class="form-control" name="link">
              </div>
              <div class="form-group" id="preview-file">
                <label for="message-text" class="control-label">File :</label>
                <input type="file" class="control-label" name="file">
              </div>
              <div class="form-group" id="preview-gps">
                <div class="form-group">
                  <label for="message-text" class="control-label">Latitude :</label>
                  <input type="text" class="form-control" name="latitude" id="lat">
                </div>
                <div class="form-group">
                  <label for="message-text" class="control-label">Longitude :</label>
                  <input type="text" class="form-control" name="longitude" id="lon">
                </div>
              </div>
              <div id="edge">
                <div class="form-group">
                  <label for="message-text" class="control-label">Source :</label>
                  <input type="text" class="form-control" name="alpha">
                </div>
                <div class="form-group">
                  <label for="message-text" class="control-label">Target :</label>
                  <input type="text" class="form-control" name="beta">
                </div>
                <div class="form-group">
                  <label for="message-text" class="control-label">Relation :</label>
                  <input type="text" class="form-control" name="relation">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 no-padding">
          <div id="sigma_container"></div>
        </div>
      </div>
    </div>
    
    <div class="modal-footer footer navbar-fixed-bottom">
      &copy; Graphisy 2015
    </div>
    
    <script src="js/jquery-2.1.3.min.js"></script>
    <script src="js/gps.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/sigma.min.js"></script>
    <script src="js/functions.js"></script>
    <script>
      var nodes;// = [{"alpha":null,"beta":null,"relation":null,"id_node":"43","name":"EDO","description":"EDO","created_on":"2015-04-18 07:55:34","is_deleted":"1","owner":"42","email":null,"link":null,"path":null,"is_previewable":null,"latitude":null,"longitude":null},{"alpha":"47","beta":"43","relation":"has","id_node":"47","name":"Analyse B","description":"Cour de math","created_on":"2015-04-18 08:18:35","is_deleted":"0","owner":"42","email":null,"link":null,"path":null,"is_previewable":null,"latitude":null,"longitude":null},{"alpha":"50","beta":"43","relation":"applies","id_node":"50","name":"(x)''= ma","description":"<h1>Power of physics<\/h1>","created_on":"2015-04-18 09:10:13","is_deleted":"0","owner":"42","email":null,"link":null,"path":null,"is_previewable":null,"latitude":null,"longitude":null},{"alpha":"43","beta":"44","relation":"has","id_node":"44","name":"Exo-edo","description":"Methode pour calculer les edo","created_on":"2015-04-18 07:57:13","is_deleted":"0","owner":"42","email":null,"link":null,"path":"pouet.png","is_previewable":"1","latitude":null,"longitude":null},{"alpha":"43","beta":"45","relation":"requires","id_node":"45","name":"Polynome","description":"Polynome","created_on":"0000-00-00 00:00:00","is_deleted":"0","owner":"42","email":null,"link":null,"path":null,"is_previewable":null,"latitude":null,"longitude":null},{"alpha":"43","beta":"46","relation":"link","id_node":"46","name":"EDO-wiki","description":"Wiki-EDO","created_on":"2015-04-18 08:12:03","is_deleted":"0","owner":"42","email":null,"link":"http:\/\/fr.wikipedia.org\/wiki\/%C3%89quation_di","path":null,"is_previewable":null,"latitude":null,"longitude":null}];
      var current_node = '43';
      var backend = 'http://deel.tk/mic/api.php';
      var s;
        
      document.body.onload = function() {
        resize();
        
        s = new sigma('sigma_container');
        s.settings({
          //font: 'Verdana',
          
          labelSize: "proportional",
          labelSizeRatio: 4,
          labelThreshold: 2,
          
          edgeColor: 'source',// 'default'/'target'
          defaultEdgeColor: 'grey',
          
          defaultLabelColor: '#000',
          defaultNodeColor: '#f00',
          defaultLabelSize: 20,
          
          zoomMin: 0.01,
          zoomMax: 4,
          mouseZoomDuration: 100,
          doubleClickZoomDuration: 100,
          
          enableHovering: true,
          enableEdgeHovering: true
        });
        s.bind('clickNode', function(e) {
          current_node = e.data.node.id;
          view(current_node);
        });
        s.bind('clickStage', function(e) {
          emptyForm(document.getElementById('form'));
        });
        view(current_node);
      };
      
    </script>
  </body>
</html>
