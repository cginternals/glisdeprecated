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
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
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
          <div class="alert alert-info">
            <button type="button" class="close" data-dismiss="alert">&times</button>
            <strong>feature line color coding:</strong>
            <span class="label label-important">unsupported</span>
            <span class="label label-success">core profile</span>
            <span class="label label-warning">compatibility profile</span>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="span8 offset2">
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

      <?php echoGLFeaturesArray(); ?>

      function process(response)
      {
        //$("#glresult").text(response.evaluate("/glquery/command/features/require/@number"));

        //$xml = response;
        //$test = $(response).find("glquery").evaluate();

        var require = parseFloat($(response).find("require[api='gl']").attr("number"));
        var remove  = parseFloat($(response).find("remove[api='gl']").attr("number"));

        for(var id in featureIDs)
        {
          if($(featureIDs[id]).hasClass('bar-success'))
            $(featureIDs[id]).removeClass('bar-success').addClass('bar-danger');
          if($(featureIDs[id]).hasClass('bar-warning'))
            $(featureIDs[id]).removeClass('bar-warning').addClass('bar-danger');
        }

        for(var id in featureIDs)
          if(parseFloat(id) >= remove)
            $(featureIDs[id]).toggleClass('bar-danger bar-warning');
          else if(parseFloat(id) >= require)
            $(featureIDs[id]).toggleClass('bar-danger bar-success');
        
        $("#glresult").text((new XMLSerializer()).serializeToString(response));
      }

      function glquery(query)
      {
        query = query.split(' ');

        $.ajax({
          url: 'glquery.php'
        , type: 'post'
        , data: { 'query': query[0] }
        , dataType: 'xml'
        , success: function(response) { process(response); }
        , error:function() { /* TODO */}});

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
          , { 'query': query, 'limit': 8 }
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