<?php require('glfeatures.php'); ?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8">
    <title>glIsDeprecated</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="css/glquery.css" rel="stylesheet" media="screen">
  </head>

  <body>
    <script src="http://code.jquery.com/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <div class="container">
      <header class="row">
        <div class="offset3 span6">
          <img src="img/glisdeprecated-logo.svg" alt="logo">
        </div>
      </header>
      
      <div class="row">
          <div class="offset2">
            <input id="glquery" class="code span8" type="text" data-provide="typeahead" autocomplete="off" placeholder="<OpenGL function, type, enum, or extension>">
          </div>
      </div>
      
      <?php echoGLFeatureBar(); ?>

      <div class="row">
        <div class="span8 offset2">
          <!--<div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert">&times</button>
            <strong>query usage:</strong> <code>&ltopengl-keyword&gt [&ltopengl-version&gt]</code> (e.g., <code>glVertex3f 3.2</code>)
          </div>-->

          <div class="alert alert-warning">
            <button type="button" class="close" data-dismiss="alert">&times</button>
            <strong>note:</strong> this service is currently work in progress. It will be a search that provides information about the deprecation status of a given OpenGL identifier. For now bootstrap ui and typeahead works, suggesting gl api only. <a href="glquery.php">glquery.php</a> already returns xml results for some queries (commands, enums, not types nor extensions). Next step is the presentation of those results. So stay tuned, and spread the word!</div>
        </div>
      </div>

      <div class="row">
        <div class="span8 offset2">
          <pre id="glresult">
          </pre>
        </div>
      </div>

    </div>

    <footer class="navbar navbar-fixed-bottom">
      <div class="container">
        <div class="offset2 span8">
          <ul class="nav nav-pills">
            <li class="disabled"><a href="#">Query</a></li>
            <li><a href="http://www.opengl.org/registry">OpenGL Registry</a></li>
            <li><a href="https://github.com/cgcostume/glisdeprecated">glIsDeprecated on GitHub</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Impressum</a></li>
          </ul>
        </div>
      </div>
    </footer>

    <script>
      function extractor(query) 
      {
        var result = /([^\s]+)$/.exec(query);
        if(result && result[1])
          return result[1].trim();
        return '';
      }

      function glquery(query)
      {
        query = query.split(' ');

        $.post(
            '/glquery.php'
          , { 'query': query[0] }
          , function(data) { $("#glresult").text(data); }); 

        $("#glresult").show();
      }

      $("#glquery").keyup(function () 
        {
          if($("#glquery").val().length == 0)
            $("#glresult").hide();
        }).keyup();

      // http://stackoverflow.com/questions/12662824/twitter-bootstrap-typeahead-multiple-values
      $("#glquery").typeahead({ minLength: 2
        , source: function(query, process) 
        {
          $.post(
            '/gltypeahead.php'
          , { 'query': extractor(query), 'limit': 8 }
          , function(data) { process(JSON.parse(data)); }); 
        }
        , updater: function(item) 
        {
          query = this.$element.val().replace(/[^\s]*$/,'') + item;
          glquery(query);

          return query;
        }
      });

      <?php echoGLFeatureBarTooltips(); ?>    

    </script> 

  </body>
</html>