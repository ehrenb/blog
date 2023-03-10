<html>
<link rel="stylesheet" href="minimal.css">
<script src="https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js"></script>
<title>Blog</title>
<?php include 'header.html'; ?>
<br>
<br>
<br>
<center>
    <br>
    <br>

    <br>
    <br>
    <h1>$Discovering code paths in APKs Pt1. </h1>
    <br>
</center>
<p>
<br>
<br>
<br>

I've been tinkering and brainstorming some new project ideas within the reverse engineering space.  One of the ideas involves automating dynamic analysis using <a href="http://appium.io/">Appium</a> for the purpose of vulnerablity discovery.  Rather than randomly fuzzing through apps, I thought it might be a good idea to inform my dynamic analysis' interactions through static analyses generated by <a href="https://github.com/ehrenb/Mercator">Mercator</a>.  The raw outputs for Mercator include two NetworkX-formatted graphs in JSON files: a full graph of the analyzed application's classes, and a subgraph containing just the Android components' classes (activities, providers, receivers, services).  The edges of the graphs represent method XREFs that Androguard was able to find from between classes.  I wanted to be able write some simple NetworkX functions that can calculate shortest paths betweeacn Android components so that during dynamic analysis I can ensure test coverage of "hard to reach" components (i.e. a service that can only be activated if a specific activity is started first) (side note: determining inputs for required forms and such should be doable through Appium, but is a problem to be explored later).

<br>
<br>

<p>I eventually plan to integrate the following into the Mercator D3 graph GUI, but for now it is exists as a standalone script that reads the Mercator component graph json.  I tested using the <a href="https://www.reverse.it/sample/9d767c41599325ccd0643d6f432b9075775a85c60df176a845605715be230263?environmentId=200">krep banking malware apk</a> (package: krep.itmtd.ywtjexf. md5: 02e231f85558f37da6802142440736f6).  Nothing too crazy, just a random binary I pulled from my stash.  The below script uses NetworkX and the generated component graph to determine the path between two components: UampleUverlayUhowUctivity, the Main Activity of the app, and MasterInterceptor, a malicious service)

<p>Using the Mercator GUI, I can see that within the onCreate funciton of UampleUverlayUhowUctivity, there is a startService call which creates a new instance of MasterInterceptor service.  Therefore, NetworkX should be able to determine a shortest path (of length 1) from UampleUverlayUhowUctivity to MasterInterceptor.  This is a very simple example case, and will scale in usefulness with more complex cases involving deeply embedded components:</p>
<br>
<br>
<img src="https://raw.githubusercontent.com/ehrenb/Mercator/master/docs/cropped.png">

<br>
<br>
<script src="https://gist.github.com/ehrenb/fbb2598fe1e0fb25d29595af57f44b4b.js"></script>


<p>The ugly graph generated by Matplotlib looks like this, confirming (the red nodes) a shortest path of 1 (onCreate) was found.  The next steps will be to get this integrated into Mercator, and optionally save outputs to inform the dynamic analysis engine.  Finally, I'm sure there are other useful algorithms to apply, so exploring those would be a good idea as well. Future ideas include: 
<ul>
    <li>backwards tracing of interesting classes (i.e. java.net.HttpURLConnection, or javax.crypto.Cipher) to source nodes (i.e. LoginActivity), to speed up TTP discovery (rather than looking at all the components for interesting behavior, start with the behavior and derive the components).  </li>
    <li> finding feature-heavy groups of components (i.e. a group of nodes that faciliate a login) by looking at connectivity to feature-specific nodes (i.e. a subgraph is heavily connected to HTTPS/auth nodes)</li>
    <li>finding unintended edges to sensitive nodes (i.e. skip login to access database).</li>
</ul>
</p>

<img src="https://raw.githubusercontent.com/ehrenb/Mercator/master/Mercator/utils/nx_scripts/graph_shortest_path.png">

<br>
<br>

The graph json output, script, and matplotlib representation can be found <a href="https://github.com/ehrenb/Mercator/tree/master/Mercator/utils/nx_scripts">here</a>.


<br>
<br>
Note: similar networkx artifacts could probably be pulled from a newer version of Androguard using <a href="https://github.com/androguard/androguard/blob/13fa6df2a79b7f3f8950ae416bf6316ddb2d5ffe/androguard/core/analysis/analysis.py">analysis.py</a>.
<br>
<br>

</p>


</p>

</html>

