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
<h1>$CSAW CTF 2018 web50 and misc100 solutions</h1>
<br>
</center>
<p>
<br>
<br>
<br>

This is my writeup for the Web 50 <a href="https://ctf.csaw.io/">CSAW CTF 2018</a> challenge named "LDAB". This challenge consists of a web form that allows users to search a LDAP database of users.
<p>
The input field is used to craft a query string the backend against the GivenName field.  Given that, and the fact that only names with OU Employees are shown, I suspected that the string formatting for the query looks like this: (&GivenName={})(OU=Employees)). 

I am able to craft a LDAP query injection with the following string: *)(|(UID=*), which makes the formatted query like: (&(GivenName=*)(|(UID=*))(OU=Employees)) .

<p>
However, this only returns the users we already have access to see because the injected query is still contained inside of a bigger set constrained by an AND statement (&).  After unsuccessful attempts to negate this, I thought that unformatted query might look like this (with a constraint on the left side that needed to be negated): (&(OU=Employees)(GivenName={})) .

<p>
<img src="res/csaw/web50.PNG" width="50%" height="50%"\>
<p>
After some trial and error, I came to this input, which ends the outer AND statement earlier, and creates an OR with OU=* that gathers and remaining users not constrained by the outer statement: *))(|(OU=*, which makes the formatted query like: (&(OU=Employees)(GivenName=*))(|(OU=*))
<p>
<img src="res/csaw/web50_2.PNG" width="50%" height="50%"\>
<p>
<p>

The Misc 100 challenge, named Algebra, challenged users that connected to solve equations for X.  This is a pretty traditional CTF challenge, and I managed to solve it in 15 lines of Python code by using <a href="https://github.com/Gallopsled/pwntools">pwntools</a> and <a href="https://github.com/sympy/sympy">sympy</a>.

<p>
In the beginning of the round, users are prompted with the following banner and challenge, subsequent equations are presented as long as the correct solution for X is sent:


<p>
<pre>
Are you a real math wiz?

nc misc.chal.csaw.io 9002

  ____                                     __ _           _            ___ ___ 
 / ___|__ _ _ __     _   _  ___  _   _    / _(_)_ __   __| |  __  __  |__ \__ \
| |   / _` | '_ \   | | | |/ _ \| | | |  | |_| | '_ \ / _` |  \ \/ /    / / / /
| |__| (_| | | | |  | |_| | (_) | |_| |  |  _| | | | | (_| |   >  <    |_| |_| 
 \____\__,_|_| |_|   \__, |\___/ \__,_|  |_| |_|_| |_|\__,_|  /_/\_\   (_) (_) 
                     |___/                                                     
**********************************************************************************
X + 2 = 25
What does X equal?: 
</pre>
<p>
<script src="https://gist.github.com/ehrenb/9a68ae847de25713bf490956530d1d2a.js"></script>

</html>
