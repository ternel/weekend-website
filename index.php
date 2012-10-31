<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
   <head>
       <title>Est-ce que c'est bientôt le week-end ?</title>
       <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
       <style type="text/css">
           body {
              text-align:center;
              padding-top:40px
           }
           img {
             border:0px;
           }
           p.msg {
             font-family:Verdana;
             font-size:40px;
             padding-bottom:40px
           }
           p#footer {
             text-align:right;
           }
       </style>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-24710178-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
   </head>

   <?php
     require_once('core.php');

   ?>
   <body>
			<p class="msg">
				<?php echo Weekend::getText() ?>
			</p>

      <?php if (false !== Weekend::checkNotWorkingDay()): ?>
      <p class="msg">
        <?php echo Weekend::getSubText() ?>
      </p>
      <?php endif; ?>


      <?php
        $msg = "Mais demain, on ne travaille pas \o/";

        if (false !== Weekend::checkTomorrowNotWorkingDay()):?>
        <p class="msg">
          <?php echo $msg; ?>
        </p>
      <?php endif; ?>
      <p id='footer'><a href='http://twitter.com/BientotLeWE'><img src='twitter.png' alt='Twitter' title='Twitter' /></a></p>
   </body>
</html>